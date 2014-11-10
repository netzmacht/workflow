<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class StepPermissionCondition can be used to limit permissions of a workflow step.
 *
 * That means that a user can only transform the transition if he has the role which is assigned to the starting step.
 *
 * If this condition is assigned it always requires that the step has a role. If no role is given it will always fail.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class StepPermissionCondition extends AbstractPermissionCondition
{
    /**
     * If a workflow is not started it does not have a current step. So you can decide if it should be allowed.
     *
     * @var bool
     */
    private $allowStartTransition = true;

    /**
     * Disallow a start transition.
     *
     * @return $this
     */
    public function disallowStartTransition()
    {
        $this->allowStartTransition = false;

        return $this;
    }

    /**
     * Allow start transitions.
     *
     * @return $this
     */
    public function allowStartTransition()
    {
        $this->allowStartTransition = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
        // workflow is not started, so no start step exists
        if (!$item->isWorkflowStarted()) {
            if ($this->allowStartTransition) {
                return true;
            }

            $errorCollection->addError(
                'transition.condition.step.failed.not-started'
            );

            return false;
        }

        $permission = $this->getStepPermission($transition, $item);

        if ($permission && $this->user->hasPermission($permission)) {
            return true;
        }

        $errorCollection->addError(
            'transition.condition.step.failed',
            array(
                $item->getCurrentStepName(),
                $permission ? ((string) $permission) : '-'
            )
        );

        return false;
    }

    /**
     * Get role of current step.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     *
     * @return Permission|null
     */
    protected function getStepPermission(Transition $transition, Item $item)
    {
        $stepName   = $item->getCurrentStepName();
        $step       = $transition->getWorkflow()->getStep($stepName);
        $permission = $step->getPermission();

        return $permission;
    }
}
