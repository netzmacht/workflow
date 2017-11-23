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

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Exception\StepNotFoundException;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFound;

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
     * Condition to supports if workflow can handle an entity.
     *
     * @var AndCondition
     */
    private $condition;

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
     * @param string $label        The label of the workflow.
     * @param array  $config       Extra config.
     */
    public function __construct(string $name, string $providerName, string $label = '', array $config = array())
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
    public function addTransition(Transition $transition, $startTransition = false): self
    {
        if (in_array($transition, $this->transitions)) {
            return $this;
        }

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
     * @throws TransitionNotFound If transition is not found.
     *
     * @return \Netzmacht\Workflow\Flow\Transition If transition is not found.
     */
    public function getTransition(string $transitionName): Transition
    {
        foreach ($this->transitions as $transition) {
            if ($transition->getName() == $transitionName) {
                return $transition;
            }
        }

        throw TransitionNotFound::withName($transitionName, $this->getName());
    }

    /**
     * Get allowed transitions for a workflow item.
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     *
     * @throws StepNotFoundException If Step does not exists.
     * @throws TransitionNotFound If transition does not exists.
     *
     * @return Transition[]|iterable
     */
    public function getAvailableTransitions(Item $item, Context $context = null): iterable
    {
        if ($context) {
            $context = $context->createCleanCopy();
        } else {
            $context = new Context();
        }

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
            function (Transition $transition) use ($item, $context) {
                return $transition->isAvailable($item, $context);
            }
        );
    }

    /**
     * Get all transitions.
     *
     * @return Transition[]|iterable
     */
    public function getTransitions(): iterable
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
    public function hasTransition(string $transitionName): bool
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
     * @param Item    $item           The workflow item.
     * @param Context $context        Transition context.
     * @param string  $transitionName The transition name.
     *
     * @return bool
     */
    public function isTransitionAvailable(
        Item $item,
        Context $context,
        string $transitionName
    ): bool {
        if (!$item->isWorkflowStarted()) {
            return $this->getStartTransition()->getName() === $transitionName;
        }

        $step = $this->getStep($item->getCurrentStepName());
        if (!$step->isTransitionAllowed($transitionName)) {
            return false;
        }

        $transition = $this->getTransition($transitionName);

        return $transition->isAvailable($item, $context);
    }

    /**
     * Add a new step to the workflow.
     *
     * @param Step $step Step to be added.
     *
     * @return $this
     */
    public function addStep(Step $step): self
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
    public function getStep(string $stepName): Step
    {
        foreach ($this->steps as $step) {
            if ($step->getName() == $stepName) {
                return $step;
            }
        }

        throw new StepNotFoundException($stepName, $this->getName());
    }

    /**
     * Check if step with a name exist.
     *
     * @param string $stepName The step name.
     *
     * @return bool
     */
    public function hasStep(string $stepName): bool
    {
        foreach ($this->steps as $step) {
            if ($step->getName() == $stepName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set transition as start transition.
     *
     * @param string $transitionName Name of start transition.
     *
     * @throws TransitionNotFound If transition is not part of the workflow.
     *
     * @return $this
     */
    public function setStartTransition(string $transitionName): self
    {
        $this->startTransition = $this->getTransition($transitionName);

        return $this;
    }

    /**
     * Get the start transition.
     *
     * @return Transition
     */
    public function getStartTransition(): Transition
    {
        return $this->startTransition;
    }

    /**
     * Get the current condition.
     *
     * @return AndCondition
     */
    public function getCondition():? AndCondition
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
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Consider if workflow is supports an entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return bool
     */
    public function supports(EntityId $entityId, $entity): bool
    {
        if (!$this->condition) {
            return true;
        }

        return $this->condition->match($this, $entityId, $entity);
    }
}
