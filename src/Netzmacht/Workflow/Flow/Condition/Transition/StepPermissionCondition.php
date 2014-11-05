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

use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
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
     * Consider if condition matches for the given entity.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        // workflow is not started, so no start step exists
        if (!$item->isWorkflowStarted()) {
            return $this->allowStartTransition;
        }

        $role = $this->getStepRole($transition, $item);

        if ($role && $this->isGranted($role)) {
            return true;
        }

        return false;
    }

    /**
     * Describes an failed condition.
     *
     * It returns an array with 2 parameters. First one is the error message code. The second one are the params to
     * be replaced in the message.
     *
     * Example return array('transition.condition.example', array('name', 'value'));
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return array
     */
    public function describeError(Transition $transition, Item $item, Context $context)
    {
        $role = null;

        if ($item->isWorkflowStarted()) {
            $role = $this->getStepRole($transition, $item);
        }

        return array(
            'transition.condition.step-permission',
            array(
                $item->getCurrentStepName(),
                $role ? $role->getFullName() : '-'
            )
        );
    }

    /**
     * Get role of current step.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     *
     * @return Role|null
     */
    protected function getStepRole(Transition $transition, Item $item)
    {
        $stepName = $item->getCurrentStepName();
        $step     = $transition->getWorkflow()->getStep($stepName);
        $role     = $step->getRole();

        return $role;
    }
}
