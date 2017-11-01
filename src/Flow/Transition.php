<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Flow\Condition\Transition\AndCondition;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Transition extends Base
{
    /**
     * Actions which will be executed during the transition.
     *
     * @var Action[]
     */
    private $actions = array();

    /**
     * Post actions which will be executed when new step is reached.
     *
     * @var Action[]
     */
    private $postActions = array();

    /**
     * The step the transition is moving to.
     *
     * @var Step
     */
    private $stepTo;

    /**
     * A pre condition which has to be passed to execute transition.
     *
     * @var AndCondition
     */
    private $preCondition;

    /**
     * A condition which has to be passed to execute the transition.
     *
     * @var AndCondition
     */
    private $condition;

    /**
     * A set of permission being assigned to the transition.
     *
     * @var Permission|null
     */
    private $permission;

    /**
     * The corresponding workflow.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Transition constructor.
     *
     * @param string   $name     Name of the element.
     * @param Workflow $workflow The workflow to which the transition belongs.
     * @param Step     $stepTo   The target step.
     * @param string   $label    Label of the element.
     * @param array    $config   Configuration values.
     */
    public function __construct($name, Workflow $workflow, Step $stepTo, $label = '', array $config = [])
    {
        parent::__construct($name, $label, $config);

        $workflow->addTransition($this);

        $this->workflow = $workflow;
        $this->stepTo   = $stepTo;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * Add an action to the transition.
     *
     * @param Action $action The added action.
     *
     * @return $this
     */
    public function addAction(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Get all actions.
     *
     * @return Action[]|iterable
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    /**
     * Add an post action to the transition.
     *
     * @param Action $action The added action.
     *
     * @return $this
     */
    public function addPostAction(Action $action): self
    {
        $this->postActions[] = $action;

        return $this;
    }

    /**
     * Get all post actions.
     *
     * @return Action[]|iterable
     */
    public function getPostActions(): iterable
    {
        return $this->postActions;
    }

    /**
     * Get the target step.
     *
     * @return Step
     */
    public function getStepTo():? Step
    {
        return $this->stepTo;
    }

    /**
     * Get the condition.
     *
     * @return Condition|null
     */
    public function getCondition():? Condition
    {
        return $this->condition;
    }

    /**
     * Add a condition.
     *
     * @param Condition $condition The new condition.
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        if (!$this->condition) {
            $this->condition = new AndCondition();
        }
        $this->condition->addCondition($condition);

        return $this;
    }

    /**
     * Get the precondition.
     *
     * @return Condition
     */
    public function getPreCondition():? Condition
    {
        return $this->preCondition;
    }

    /**
     * Add a precondition precondition.
     *
     * @param Condition $preCondition The new precondition.
     *
     * @return $this
     */
    public function addPreCondition(Condition $preCondition): self
    {
        if (!$this->preCondition) {
            $this->preCondition = new AndCondition();
        }

        $this->preCondition->addCondition($preCondition);

        return $this;
    }

    /**
     * Consider if user input is required.
     *
     * @param Item $item Workflow item.
     *
     * @return array
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        if (!$this->actions) {
            return [];
        }

        return array_merge(
            ... array_map(
                function (Action $action) use ($item) {
                    return $action->getRequiredPayloadProperties($item);
                },
                $this->actions
            )
        );
    }

    /**
     * Consider if transition is allowed.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function isAllowed(Item $item, Context $context, ErrorCollection $errorCollection): bool
    {
        if ($this->checkPreCondition($item, $context, $errorCollection)) {
            return $this->checkCondition($item, $context, $errorCollection);
        }

        return false;
    }

    /**
     * Consider if transition is available.
     *
     * If a transition can be available but it is not allowed depending on the user input.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function isAvailable(Item $item, Context $context, ErrorCollection $errorCollection): bool
    {
        if ($this->getRequiredPayloadProperties($item)) {
            return $this->checkPreCondition($item, $context, $errorCollection);
        }

        return $this->isAllowed($item, $context, $errorCollection);
    }

    /**
     * Check the precondition.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function checkPreCondition(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        return $this->performConditionCheck($this->preCondition, $item, $context, $errorCollection);
    }

    /**
     * Check the condition.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function checkCondition(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        return $this->performConditionCheck($this->condition, $item, $context, $errorCollection);
    }

    /**
     * Set a permission to the transition.
     *
     * @param Permission $permission Permission being assigned.
     *
     * @return $this
     */
    public function setPermission(Permission $permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Consider if permission is assigned to transition.
     *
     * @param Permission $permission Permission being check.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission)
    {
        if ($this->permission) {
            return $this->permission->equals($permission);
        }

        return false;
    }

    /**
     * Get assigned permission. Returns null if no transition is set.
     *
     * @return Permission|null
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Execute all actions.
     *
     * @param Item            $item            The workflow item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function executeActions(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        return $this->doExecuteActions($item, $context, $errorCollection, $this->actions);
    }

    /**
     * Execute all actions.
     *
     * @param Item            $item            The workflow item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function executePostActions(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        return $this->doExecuteActions($item, $context, $errorCollection, $this->postActions);
    }

    /**
     * Perform condition check.
     *
     * @param Condition|null  $condition       Condition to be checked.
     * @param Item            $item            Workflow item.
     * @param Context         $context         Condition context.
     * @param ErrorCollection $errorCollection Error collection.
     *
     * @return bool
     */
    private function performConditionCheck($condition, $item, $context, ErrorCollection $errorCollection)
    {
        if (!$condition) {
            return true;
        }

        return $condition->match($this, $item, $context, $errorCollection);
    }

    /**
     * Execute the actions.
     *
     * @param Item            $item            Workflow item.
     * @param Context         $context         Condition context.
     * @param ErrorCollection $errorCollection Error collection.
     * @param Action[]        $actions         Action to execute.
     *
     * @return bool
     */
    private function doExecuteActions(Item $item, Context $context, ErrorCollection $errorCollection, $actions)
    {
        $success = $this->isAllowed($item, $context, $errorCollection);

        if ($success) {
            try {
                foreach ($actions as $action) {
                    $action->transit($this, $item, $context);
                }
            } catch (ActionFailedException $e) {
                $params = array('exception' => $e->getMessage());
                $errorCollection->addError('transition.action.failed', $params);

                return false;
            }
        }

        return $success;
    }
}
