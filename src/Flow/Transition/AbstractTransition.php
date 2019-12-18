<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2019 netzmacht David Molineus
 * @license    LGPL 3.0-or-later https://github.com/netzmacht/workflow/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Transition;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\AndCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Base transition implementation
 */
abstract class AbstractTransition extends Base implements Transition
{
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
     * @param string    $name     Name of the element.
     * @param Workflow  $workflow The workflow to which the transition belongs.
     * @param Step|null $stepTo   The target step.
     * @param string    $label    Label of the element.
     * @param array     $config   Configuration values.
     */
    public function __construct(string $name, Workflow $workflow, ?Step $stepTo, string $label = '', array $config = [])
    {
        parent::__construct($name, $label, $config);

        $workflow->addTransition($this);

        $this->workflow = $workflow;
        $this->stepTo   = $stepTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function getStepTo():? Step
    {
        return $this->stepTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition():? Condition
    {
        return $this->condition;
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(Condition $condition): Transition
    {
        if (!$this->condition) {
            $this->condition = new AndCondition();
        }
        $this->condition->addCondition($condition);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreCondition():? Condition
    {
        return $this->preCondition;
    }

    /**
     * {@inheritdoc}
     */
    public function addPreCondition(Condition $preCondition): Transition
    {
        if (!$this->preCondition) {
            $this->preCondition = new AndCondition();
        }

        $this->preCondition->addCondition($preCondition);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        if (empty($this->actions)) {
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
     * {@inheritdoc}
     */
    public function isAllowed(Item $item, Context $context): bool
    {
        if ($this->checkPreCondition($item, $context)) {
            return $this->checkCondition($item, $context);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(Item $item, Context $context): bool
    {
        if ($this->getRequiredPayloadProperties($item)) {
            return $this->checkPreCondition($item, $context);
        }

        return $this->isAllowed($item, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function checkPreCondition(Item $item, Context $context): bool
    {
        return $this->performConditionCheck($this->preCondition, $item, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCondition(Item $item, Context $context): bool
    {
        return $this->performConditionCheck($this->condition, $item, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission(Permission $permission): Transition
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission(Permission $permission): bool
    {
        if ($this->permission) {
            return $this->permission->equals($permission);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission():? Permission
    {
        return $this->permission;
    }

    /**
     * Perform condition check.
     *
     * @param Condition|null $condition Condition to be checked.
     * @param Item           $item      Workflow item.
     * @param Context        $context   Condition context.
     *
     * @return bool
     */
    private function performConditionCheck(?Condition $condition, $item, $context): bool
    {
        if (!$condition) {
            return true;
        }

        return $condition->match($this, $item, $context);
    }
}
