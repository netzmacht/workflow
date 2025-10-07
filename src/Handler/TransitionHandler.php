<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Throwable;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 */
interface TransitionHandler
{
    /** Get the workflow. */
    public function getWorkflow(): Workflow;

    /**
     * Get the item.
     */
    public function getItem(): Item;

    /**
     * Get the transition.
     *
     * @throws TransitionNotFound If transition was not found.
     */
    public function getTransition(): Transition;

    /**
     * Get the current step. Will return null if a workflow is not started yet.
     */
    public function getCurrentStep(): Step|null;

    /**
     * Consider if it handles a start transition.
     */
    public function isWorkflowStarted(): bool;

    /**
     * Consider if input is required.
     *
     * @return list<string>
     */
    public function getRequiredPayloadProperties(): array;

    /**
     * Consider if transition is available.
     */
    public function isAvailable(): bool;

    /**
     * Get the context.
     */
    public function getContext(): Context;

    /**
     * Validate the input.
     *
     * @param array<string, mixed> $payload The payload.
     */
    public function validate(array $payload = []): bool;

    /**
     * Transit to the next step.
     *
     * @throws WorkflowException For a workflow-specific error.
     * @throws Throwable         For any error caused maybe by 3rd party code in the actions.
     */
    public function transit(): State;
}
