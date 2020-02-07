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

use Assert\Assertion;
use const E_USER_DEPRECATED;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use function trigger_error;

/**
 * Class Item stores workflow related data of an entity. It knows the state history and the current state.
 *
 * @package Netzmacht\Workflow
 */
class Item
{
    /**
     * Workflow name.
     *
     * @var string
     */
    private $workflowName;

    /**
     * Current step name.
     *
     * @var string
     */
    private $currentStepName;

    /**
     * State history which is already persisted.
     *
     * @var State[]
     */
    private $stateHistory = [];

    /**
     * Recorded state changes not persisted yet.
     *
     * @var State[]
     */
    private $recordedStateChanges = [];

    /**
     * Workflow entity.
     *
     * @var mixed
     */
    private $entity;

    /**
     * Entity id.
     *
     * @var EntityId
     */
    private $entityId;

    /**
     * Construct. Do not used constructor. Use named constructor static methods.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity for which the workflow is started.
     */
    protected function __construct(EntityId $entityId, $entity)
    {
        $this->entityId = $entityId;
        $this->entity   = $entity;
    }

    /**
     * Initialize a new workflow item.
     *
     * It is called before the workflow is started.
     *
     * @param EntityId $entityId The entity id for the containing entity.
     * @param mixed    $entity   The entity for which the workflow is started.
     *
     * @return Item
     */
    public static function initialize(EntityId $entityId, $entity): self
    {
        return new Item($entityId, $entity);
    }

    /**
     * Restore an existing item.
     *
     * @param EntityId         $entityId     The entity id.
     * @param mixed            $entity       The entity.
     * @param State[]|iterable $stateHistory Set or already passed states.
     *
     * @return Item
     */
    public static function reconstitute(EntityId $entityId, $entity, iterable $stateHistory): Item
    {
        Assertion::allIsInstanceOf($stateHistory, State::class);

        $item = self::initialize($entityId, $entity);

        // replay states
        foreach ($stateHistory as $state) {
            $item->apply($state);
        }

        return $item;
    }

    /**
     * Start an item and return current state.
     *
     * @param Transition $transition The transition being executed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The transition success.
     *
     * @return State
     *
     * @throws WorkflowException If workflow is already started.
     */
    public function start(
        Transition $transition,
        Context $context,
        bool $success
    ): State {
        $this->guardNotStarted();

        $state = State::start($this->entityId, $transition, $context, $success);
        $this->record($state);

        return $state;
    }

    /**
     * Transits to a new state and return it.
     *
     * @param Transition $transition The transition being executed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The transition success.
     *
     * @throws WorkflowException If workflow is not started.
     *
     * @return State
     */
    public function transit(
        Transition $transition,
        Context $context,
        bool $success
    ): State {
        $this->guardStarted();

        $state = $this->getLatestState();
        $state = $state->transit($transition, $context, $success);

        $this->record($state);

        return $state;
    }

    /**
     * Release the recorded state changes.
     *
     * Reset the internal recorded state changes and return them.
     *
     * @return State[]|iterable
     */
    public function releaseRecordedStateChanges() : iterable
    {
        $recordedStates             = $this->recordedStateChanges;
        $this->recordedStateChanges = [];

        return $recordedStates;
    }

    /**
     * Get the name of the current step.
     *
     * @return string
     */
    public function getCurrentStepName(): ?string
    {
        return $this->currentStepName;
    }

    /**
     * Get the entity id.
     *
     * @return EntityId
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get the entity.
     *
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the state history of the workflow item.
     *
     * @return State[]|iterable
     */
    public function getStateHistory(): iterable
    {
        return $this->stateHistory;
    }

    /**
     * Get latest successful state.
     *
     * @param bool $successfulOnly Return only success ful steps.
     *
     * @return State|false
     *
     * @deprecated Use getLatestStateOccurred() or getLatestState() instead.
     */
    public function getLatestState(bool $successfulOnly = true)
    {
        // @codingStandardsIgnoreStart
        @trigger_error(
            __METHOD__ . ' is deprecated. Use getLatestStateOccurred() or getLatestState() instead.',
            E_USER_DEPRECATED
        );
        // @codingStandardsIgnoreEnd

        if (!$successfulOnly) {
            return $this->getLatestStateOccurred();
        }

        return $this->getLatestSuccessfulState() ?: false;
    }

    /**
     * Get latest state which occurred no matter if successful or not.
     *
     * @return State|null
     */
    public function getLatestStateOccurred(): ?State
    {
        if (count($this->stateHistory) === 0) {
            return null;
        }

        $index = (count($this->stateHistory) - 1);

        return $this->stateHistory[$index];
    }

    /**
     * Get latest successful state which occurred.
     *
     * @return State|null
     */
    public function getLatestSuccessfulState(): ?State
    {
        for ($index = (count($this->stateHistory) - 1); $index >= 0; $index--) {
            if ($this->stateHistory[$index]->isSuccessful()) {
                return $this->stateHistory[$index];
            }
        }

        return null;
    }

    /**
     * Get name of the workflow.
     *
     * @return string
     */
    public function getWorkflowName(): ?string
    {
        return $this->workflowName;
    }

    /**
     * Consider if workflow has started.
     *
     * @return bool
     */
    public function isWorkflowStarted(): bool
    {
        return !empty($this->currentStepName);
    }

    /**
     * Detach item from current workflow.
     *
     * You should only use it with care if the workflow has changed and there is no way to finish it.
     *
     * @return void
     */
    public function detach(): void
    {
        $this->currentStepName = null;
        $this->workflowName    = null;
    }

    /**
     * Guard that workflow of item was not already started.
     *
     * @return void
     *
     * @throws FlowException If item workflow process was already started.
     */
    private function guardNotStarted(): void
    {
        if ($this->isWorkflowStarted()) {
            throw new FlowException('Item is already started.');
        }
    }

    /**
     * Guard that workflow of item is started.
     *
     * @return void
     *
     * @throws FlowException If item workflow process was not started.
     */
    private function guardStarted(): void
    {
        if (!$this->isWorkflowStarted()) {
            throw new FlowException('Item has not started yet.');
        }
    }

    /**
     * Record a new state change.
     *
     * @param State $state The state being assigned.
     *
     * @return void
     */
    private function record(State $state) : void
    {
        $this->recordedStateChanges[] = $state;
        $this->apply($state);
    }

    /**
     * Apply a new state.
     *
     * @param State $state The state being assigned.
     *
     * @return void
     */
    private function apply(State $state): void
    {
        // only change current step if transition was successful
        if ($state->isSuccessful()) {
            $this->currentStepName = $state->getStepName();
            $this->workflowName    = $state->getWorkflowName();
        } elseif (!$this->isWorkflowStarted()) {
            $this->workflowName = $state->getWorkflowName();
        }

        $this->stateHistory[] = $state;
    }
}
