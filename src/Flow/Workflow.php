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

namespace Netzmacht\Workflow\Flow;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\RoleNotFoundException;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition;
use Netzmacht\Workflow\Security\Role;

/**
 * Class Workflow stores all information of a step processing workflow.
 *
 * @package Netzmacht\Workflow\Flow
 */
class Workflow extends Base
{
    /**
     * Transitions being available in the workflow.
     *
     * @var Transition[]
     */
    private $transitions = array();

    /**
     * Steps being available in the workflow.
     *
     * @var Step[]
     */
    private $steps = array();

    /**
     * The start transition.
     *
     * @var Transition
     */
    private $startTransition;

    /**
     * Condition to match if workflow can handle an entity.
     *
     * @var AndCondition
     */
    private $condition;

    /**
     * Acl roles.
     *
     * @var Role[]
     */
    private $roles;

    /**
     * Name of the provider.
     *
     * @var string
     */
    private $providerName;

    /**
     * Construct.
     *
     * @param string $name         The name of the workflow.
     * @param string $providerName Name of the provider.
     * @param null   $label        The label of the workflow.
     * @param array  $config       Extra config.
     */
    public function __construct($name, $providerName, $label = null, array $config = array())
    {
        parent::__construct($name, $label, $config);

        $this->providerName = $providerName;
    }

    /**
     * Add a transition to the workflow.
     *
     * @param Transition $transition      Transition to be added.
     * @param bool       $startTransition True if transition will be the start transition.
     *
     * @return $this
     */
    public function addTransition(Transition $transition, $startTransition = false)
    {
        $transition->setWorkflow($this);
        $this->transitions[] = $transition;

        if ($startTransition) {
            $this->startTransition = $transition;
        }

        return $this;
    }

    /**
     * Get a transition by name.
     *
     * @param string $transitionName The name of the transition.
     *
     * @throws TransitionNotFoundException If transition is not found.
     *
     * @return \Netzmacht\Workflow\Flow\Transition If transition is not found.
     */
    public function getTransition($transitionName)
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getName() == $transitionName) {
                return $transition;
            }
        }

        throw new TransitionNotFoundException($transitionName, $this->getName());
    }

    /**
     * Get allowed transitions for a workflow item.
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     *
     * @throws StepNotFoundException If Step does not exists.
     * @throws TransitionNotFoundException If transition does not exists.
     *
     * @return array
     */
    public function getAvailableTransitions(Item $item, Context $context = null)
    {
        $context         = $context ?: new Context();
        $errorCollection = new ErrorCollection();

        if (!$item->isWorkflowStarted()) {
            $transitions = array($this->getStartTransition());
        } else {
            $step        = $this->getStep($item->getCurrentStepName());
            $transitions = array_map(
                function ($transitionName) {
                    return $this->getTransition($transitionName);
                },
                $step->getAllowedTransitions()
            );
        }

        return array_filter(
            $transitions,
            function (Transition $transition) use ($item, $context, $errorCollection) {
                return $transition->isAvailable($item, $context, $errorCollection);
            }
        );
    }

    /**
     * Get all transitions.
     *
     * @return Transition[]
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Check if transition is part of the workflow.
     *
     * @param string $transitionName Transition name.
     *
     * @return bool
     */
    public function hasTransition($transitionName)
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getName() === $transitionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a specific transition is available.
     *
     * @param Item            $item            The workflow item.
     * @param Context         $context         Transition context.
     * @param ErrorCollection $errorCollection Error collection.
     * @param string          $transitionName  The transition name.
     *
     * @return bool
     */
    public function isTransitionAvailable(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        $transitionName
    ) {
        if (!$item->isWorkflowStarted()) {
            return $this->getStartTransition()->getName() === $transitionName;
        }

        $step = $this->getStep($item->getCurrentStepName());
        if (!$step->isTransitionAllowed($transitionName)) {
            return false;
        }

        $transition = $this->getTransition($transitionName);
        return $transition->isAvailable($item, $context, $errorCollection);
    }

    /**
     * Add a new step to the workflow.
     *
     * @param Step $step Step to be added.
     *
     * @return $this
     */
    public function addStep(Step $step)
    {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Get a step by step name.
     *
     * @param string $stepName The step name.
     *
     * @return Step
     *
     * @throws StepNotFoundException If step is not found.
     */
    public function getStep($stepName)
    {
        foreach ($this->steps as $step) {
            if ($step->getName() == $stepName) {
                return $step;
            }
        }

        throw new StepNotFoundException($stepName, $this->getName());
    }

    /**
     * Set transition as start transition.
     *
     * @param string $transitionName Name of start transition.
     *
     * @throws TransitionNotFoundException If transition is not part of the workflow.
     *
     * @return $this
     */
    public function setStartTransition($transitionName)
    {
        $this->startTransition = $this->getTransition($transitionName);

        return $this;
    }

    /**
     * Get the start transition.
     *
     * @return Transition
     */
    public function getStartTransition()
    {
        return $this->startTransition;
    }

    /**
     * Add an acl role.
     *
     * @param Role $role Role to be added.
     *
     * @return $this
     */
    public function addRole(Role $role)
    {
        $this->guardWorkflowRole($role);

        $this->roles[] = $role;

        return $this;
    }

    /**
     * Get A role by its name.
     *
     * @param string $roleName Name of the role being requested.
     *
     * @return Role
     *
     * @throws RoleNotFoundException If role is not set.
     */
    public function getRole($roleName)
    {
        foreach ($this->roles as $role) {
            if ($role->getName() == $roleName) {
                return $role;
            }
        }

        throw new RoleNotFoundException($roleName);
    }

    /**
     * Get all available roles.
     *
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get the current condition.
     *
     * @return AndCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Shortcut to add a condition to the condition collection.
     *
     * @param Condition $condition Condition to be added.
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
     * Get provider name.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Consider if workflow is responsible for the entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return bool
     */
    public function match(EntityId $entityId, $entity)
    {
        if (!$this->condition) {
            return true;
        }

        return $this->condition->match($this, $entityId, $entity);
    }

    /**
     * Gard that role belongs to the workflow.
     *
     * @param Role $role Role to be a valid workflow role.
     *
     * @return void
     *
     * @throws InvalidArgumentException If role is not the same workflow.
     */
    private function guardWorkflowRole(Role $role)
    {
        Assertion::eq($role->getWorkflowName(), $this->getName());
    }
}
