<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

use DateTimeImmutable;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\FlowException;

use function sprintf;

/**
 * Class State stores information about the current state of an entity.
 *
 * @psalm-suppress ClassMustBeFinal
 * @psalm-import-type TErrorArray from ErrorCollection
 */
final class State
{
    /**
     * The state id.
     */
    private int|null $stateId;

    /**
     * The entity id.
     */
    private EntityId $entityId;

    /**
     * Store if the transition was successful.
     */
    private bool $successful;

    /**
     * The last transition.
     */
    private string $transitionName;

    /**
     * The current step.
     */
    private string $stepName;

    /**
     * Date being stored.
     *
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * Date when state was reached.
     */
    private DateTimeImmutable $reachedAt;

    /**
     * List of errors.
     *
     * @var TErrorArray
     */
    private array $errors;

    /**
     * Name of start workflow.
     */
    private string $startWorkflowName;

    /**
     * Name of the target workflow.
     */
    private string $targetWorkflowName;

    /**
     * Construct.
     *
     * @param EntityId             $entityId           The entity id.
     * @param string               $startWorkflowName  Workflow name of the start point.
     * @param string               $transitionName     The transition executed to reach the step.
     * @param string               $stepToName         The step reached after transition.
     * @param bool                 $successful         Consider if a transition was successful.
     * @param array<string, mixed> $data               Stored data.
     * @param DateTimeImmutable    $reachedAt          Time when state was reached.
     * @param TErrorArray          $errors             List of errors.
     * @param int|null             $stateId            The state id of a persisted state.
     * @param string|null          $targetWorkflowName Workflow name of the target point. Allow null for BC reasons.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityId $entityId,
        string $startWorkflowName,
        string $transitionName,
        string $stepToName,
        bool $successful,
        array $data,
        DateTimeImmutable $reachedAt,
        array $errors = [],
        int|null $stateId = null,
        string|null $targetWorkflowName = null,
    ) {
        $this->entityId           = $entityId;
        $this->startWorkflowName  = $startWorkflowName;
        $this->transitionName     = $transitionName;
        $this->stepName           = $stepToName;
        $this->successful         = $successful;
        $this->data               = $data;
        $this->reachedAt          = $reachedAt;
        $this->errors             = $errors;
        $this->stateId            = $stateId;
        $this->targetWorkflowName = $targetWorkflowName ?? $startWorkflowName;
    }

    /**
     * Create an initial state.
     *
     * @param EntityId   $entityId   The entity id.
     * @param Transition $transition The current executed transition.
     * @param Context    $context    The context.
     * @param bool       $success    Success state.
     *
     * @throws FlowException When transition has no target step.
     */
    public static function start(
        EntityId $entityId,
        Transition $transition,
        Context $context,
        bool $success,
    ): State {
        $stepTo = $transition->getStepTo();

        if ($stepTo === null) {
            throw new FlowException(
                sprintf('Failed to start workflow. Transition "%s" has no target step', $transition->getName()),
            );
        }

        $workflowName = $stepTo->getWorkflowName() ?? $transition->getWorkflow()->getName();

        return new State(
            $entityId,
            $workflowName,
            $transition->getName(),
            $stepTo->getName(),
            $success,
            $context->getProperties()->toArray(),
            new DateTimeImmutable(),
            $context->getErrorCollection()->toArray(),
            null,
            $workflowName,
        );
    }

    /**
     * Get step name.
     */
    public function getStepName(): string
    {
        return $this->stepName;
    }

    /**
     * Get the transition name.
     */
    public function getTransitionName(): string
    {
        return $this->transitionName;
    }

    /**
     * Get the current workflow name.
     *
     * If the state transition was successful, the target workflow name is returned, otherwise the start workflow name.
     */
    public function getWorkflowName(): string
    {
        if ($this->isSuccessful()) {
            return $this->getTargetWorkflowName();
        }

        return $this->getStartWorkflowName();
    }

    /**
     * Get the start workflow name.
     */
    public function getStartWorkflowName(): string
    {
        return $this->startWorkflowName;
    }

    /**
     * Get the target workflow name.
     */
    public function getTargetWorkflowName(): string
    {
        return $this->targetWorkflowName;
    }

    /**
     * Get state data.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get reached-at-time.
     */
    public function getReachedAt(): DateTimeImmutable
    {
        return $this->reachedAt;
    }

    /**
     * Consider if the state is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * Get the entity id.
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get error messages.
     *
     * @return TErrorArray
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get state id.
     */
    public function getStateId(): int|null
    {
        return $this->stateId;
    }

    /**
     * Transit to a new state.
     *
     * @param Transition $transition The transition being performed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The success state.
     *
     * @throws FlowException When transition fails.
     */
    public function transit(
        Transition $transition,
        Context $context,
        bool $success = true,
    ): State {
        $dateTime           = new DateTimeImmutable();
        $stepName           = $this->stepName;
        $workflowName       = $this->getWorkflowName();
        $targetWorkflowName = $workflowName;

        if ($success) {
            $stepTo = $transition->getStepTo();
            if ($stepTo === null) {
                throw new FlowException(
                    sprintf('Failed to transit state. Transition "%s" has no target step', $transition->getName()),
                );
            }

            $targetWorkflowName = $stepTo->getWorkflowName() ?? $transition->getWorkflow()->getName();
            $stepName           = $stepTo->getName();
        }

        $properties = $context->getProperties();

        return new self(
            $this->entityId,
            $workflowName,
            $transition->getName(),
            $stepName,
            $success,
            $properties->toArray(),
            $dateTime,
            $context->getErrorCollection()->toArray(),
            null,
            $targetWorkflowName,
        );
    }
}
