<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

use Assert\Assertion;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Exception\FlowException;

use function assert;
use function count;

/**
 * Class Item stores workflow-related data of an entity. It knows the state history and the current state.
 */
final class Item
{
    /**
     * Workflow name.
     */
    private string|null $workflowName = null;

    /**
     * Current step name.
     */
    private string|null $currentStepName = null;

    /**
     * State history which is already persisted.
     *
     * @var State[]
     */
    private array $stateHistory = [];

    /**
     * Recorded state changes aren't persisted yet.
     *
     * @var State[]
     */
    private array $recordedStateChanges = [];

    /**
     * Workflow entity.
     */
    private mixed $entity;

    /**
     * Entity id.
     */
    private EntityId $entityId;

    /**
     * Construct. Do not used constructor. Use named constructor static methods.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity for which the workflow is started.
     */
    protected function __construct(EntityId $entityId, mixed $entity)
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
    public static function initialize(EntityId $entityId, mixed $entity): self
    {
        return new Item($entityId, $entity);
    }

    /**
     * Restore an existing item.
     *
     * @param EntityId         $entityId     The entity id.
     * @param mixed            $entity       The entity.
     * @param State[]|iterable $stateHistory Set or already passed states.
     */
    public static function reconstitute(EntityId $entityId, mixed $entity, iterable $stateHistory): Item
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
     * Start an item and return the current state.
     *
     * @param Transition $transition The transition being executed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The transition success.
     *
     * @throws WorkflowException If the workflow is already started.
     */
    public function start(
        Transition $transition,
        Context $context,
        bool $success,
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
     * @throws WorkflowException If the workflow is not started.
     */
    public function transit(
        Transition $transition,
        Context $context,
        bool $success,
    ): State {
        $this->guardStarted();

        $state = $this->getLatestSuccessfulState();
        assert($state instanceof State);
        $state = $state->transit($transition, $context, $success);

        $this->record($state);

        return $state;
    }

    /**
     * Release the recorded state changes.
     *
     * Reset the internal recorded state changes and return them.
     *
     * @return iterable<State>
     */
    public function releaseRecordedStateChanges(): iterable
    {
        $recordedStates             = $this->recordedStateChanges;
        $this->recordedStateChanges = [];

        return $recordedStates;
    }

    /**
     * Get the name of the current step.
     */
    public function getCurrentStepName(): string|null
    {
        return $this->currentStepName;
    }

    /**
     * Get the entity id.
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get the entity.
     */
    public function getEntity(): mixed
    {
        return $this->entity;
    }

    /**
     * Get the state history of the workflow item.
     *
     * @return iterable<State>
     */
    public function getStateHistory(): iterable
    {
        return $this->stateHistory;
    }

    /**
     * Get latest state which occurred no matter if successful or not.
     */
    public function getLatestStateOccurred(): State|null
    {
        if (count($this->stateHistory) === 0) {
            return null;
        }

        $index = count($this->stateHistory) - 1;

        return $this->stateHistory[$index];
    }

    /**
     * Get latest successful state which occurred.
     */
    public function getLatestSuccessfulState(): State|null
    {
        for ($index = count($this->stateHistory) - 1; $index >= 0; $index--) {
            if ($this->stateHistory[$index]->isSuccessful()) {
                return $this->stateHistory[$index];
            }
        }

        return null;
    }

    /**
     * Get name of the workflow.
     */
    public function getWorkflowName(): string|null
    {
        return $this->workflowName;
    }

    /**
     * Consider if the workflow has started.
     */
    public function isWorkflowStarted(): bool
    {
        return $this->currentStepName !== null;
    }

    /**
     * Detach item from the current workflow.
     *
     * You should only use it with care if the workflow has changed and there is no way to finish it.
     */
    public function detach(): void
    {
        $this->currentStepName = null;
        $this->workflowName    = null;
    }

    /**
     * Guard that workflow of item was not already started.
     *
     * @throws FlowException If an item workflow process was already started.
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
     * @throws FlowException If an item workflow process was not started.
     */
    private function guardStarted(): void
    {
        if (! $this->isWorkflowStarted()) {
            throw new FlowException('Item has not started yet.');
        }
    }

    /**
     * Record a new state change.
     *
     * @param State $state The state being assigned.
     */
    private function record(State $state): void
    {
        $this->recordedStateChanges[] = $state;
        $this->apply($state);
    }

    /**
     * Apply a new state.
     *
     * @param State $state The state being assigned.
     */
    private function apply(State $state): void
    {
        // only change current step if transition was successful
        if ($state->isSuccessful()) {
            $this->currentStepName = $state->getStepName();
            $this->workflowName    = $state->getWorkflowName();
        } elseif (! $this->isWorkflowStarted()) {
            $this->workflowName = $state->getWorkflowName();
        }

        $this->stateHistory[] = $state;
    }
}
