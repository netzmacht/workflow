<?php

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Base;
use Netzmacht\Workflow\Flow\Exception\RoleNotFoundException;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;

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
     * @var string
     */
    private $providerName;

    /**
     * Construct.
     *
     * @param string      $name   The name of the workflow.
     * @param null|string $providerName
     * @param null        $label  The label of the workflow.
     * @param array       $config Extra config.
     * @param null        $modelId
     */
    public function __construct($name, $providerName, $label = null, array $config = array(), $modelId = null)
    {
        parent::__construct($name, $label, $config, $modelId);

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
     * @param Entity $entity The entity.
     *
     * @return bool
     */
    public function match(Entity $entity)
    {
        if (!$this->condition) {
            return true;
        }

        return $this->condition->match($this, $entity);
    }

    /**
     * Transit the entity to a new state.
     *
     * @param Item    $item         The entity.
     * @param string  $transitionName The transition name.
     * @param Context $context        The context of the transition.
     *
     * @throws StepNotFoundException         If a workflow related issue occurs.
     *
     * @return State
     */
    public function transit(Item $item, $transitionName, Context $context)
    {
        $this->guardWorkflowStarted($item);

        $currentStep = $this->getStep($item->getCurrentStepName());

        $this->guardTransitionAllowed($currentStep, $transitionName);

        $transition = $this->getTransition($transitionName);

        return $transition->transit($item, $context);
    }

    /**
     * Start a workflow.
     *
     * If the workflow is already started, nothing happens.
     *
     * @param Item    $item    The entity.
     * @param Context $context The transition context.
     *
     * @return State
     */
    public function start(Item $item, Context $context)
    {
        if ($item->isWorkflowStarted()) {
            return $item->getLatestState();
        }

        $transition = $this->getStartTransition();

        return $transition->start($item, $context);
    }

    /**
     * Guard that workflow has already started.
     *
     * @param Item $item The entity.
     *
     * @throws WorkflowException If workflow has not started yet.
     *
     * @return void
     */
    private function guardWorkflowStarted(Item $item)
    {
        if (!$item->isWorkflowStarted()) {
            throw new WorkflowException(
                sprintf('Workflow for item "%s" was not started', $item->getEntity()->getEntityId())
            );
        }
    }

    /**
     * Guard that transition is allowed.
     *
     * @param Step   $currentStep    The current step.
     * @param string $transitionName The name of the transition.
     *
     * @throws WorkflowException If transition is not allowed.
     *
     * @return void
     */
    private function guardTransitionAllowed(Step $currentStep, $transitionName)
    {
        if (!$currentStep->isTransitionAllowed($transitionName)) {
            throw new WorkflowException(
                sprintf('Transition "%s" is not allowed for step "%s"', $transitionName, $currentStep->getName())
            );
        }
    }
}
