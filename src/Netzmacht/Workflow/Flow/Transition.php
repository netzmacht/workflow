<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Flow\Condition\Transition\AndCondition;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Form\Form;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @package Netzmacht\Workflow\Flow
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
     * This method should not be called.
     *
     * It's used to set the workflow reference when transition is added to the workflow.
     *
     * @param Workflow $workflow Current workflow.
     *
     * @return $this
     */
    public function setWorkflow(Workflow $workflow)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
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
    public function addAction(Action $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Get all actions.
     *
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set the target step.
     *
     * @param Step $step The target step.
     *
     * @return $this
     */
    public function setStepTo(Step $step)
    {
        $this->stepTo = $step;

        return $this;
    }

    /**
     * Get the condition.
     *
     * @return Condition
     */
    public function getCondition()
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
    public function getPreCondition()
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
    public function addPreCondition(Condition $preCondition)
    {
        if (!$this->preCondition) {
            $this->preCondition = new AndCondition();
        }

        $this->preCondition->addCondition($preCondition);

        return $this;
    }

    /**
     * Get the target step.
     *
     * @return Step
     */
    public function getStepTo()
    {
        return $this->stepTo;
    }

    /**
     * Build the form.
     *
     * @param Form $form The form being build.
     * @param Item $item The workflow item.
     *
     * @return $this
     */
    public function buildForm(Form $form, Item $item)
    {
        foreach ($this->actions as $action) {
            $action->buildForm($form, $item);
        }

        return $this;
    }

    /**
     * Consider if user input is required.
     *
     * @param Item $item Workflow item.
     *
     * @return bool
     */
    public function isInputRequired(Item $item)
    {
        foreach ($this->actions as $action) {
            if ($action->isInputRequired($item)) {
                return true;
            }
        }

        return false;
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
    public function isAllowed(Item $item, Context $context, ErrorCollection $errorCollection)
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
    public function isAvailable(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        if ($this->isInputRequired($item)) {
            return $this->checkPreCondition($item, $context, $errorCollection);
        }

        return $this->isAllowed($item, $context, $errorCollection);
    }

    /**
     * Start a transition.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return State
     */
    public function start(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        if ($item->isWorkflowStarted()) {
            return $item->getLatestState();
        }

        $success = $this->executeActions($item, $context, $errorCollection);
        $state   = $item->start($this, $context, $errorCollection, $success);

        return $state;
    }

    /**
     * Transit an Item using this transition.
     *
     * @param Item            $item            The Item.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @throws WorkflowException If process was not started yet.
     *
     * @return State
     */
    public function transit(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        $success = $this->executeActions($item, $context, $errorCollection);
        $state   = $item->transit($this, $context, $errorCollection, $success);

        return $state;
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
    private function executeActions(Item $item, Context $context, ErrorCollection $errorCollection)
    {
        $success = $this->isAllowed($item, $context, $errorCollection);

        if ($success) {
            try {
                foreach ($this->actions as $action) {
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
}
