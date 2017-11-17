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

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFound;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Workflow
 */
interface TransitionHandler
{
    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow;

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem(): Item;

    /**
     * Get the transition.
     *
     * @return Transition
     *
     * @throws TransitionNotFound If transition was not found.
     */
    public function getTransition(): Transition;

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return Step|null
     */
    public function getCurrentStep():? Step;

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted(): bool;

    /**
     * Consider if input is required.
     *
     * @return array
     */
    public function getRequiredPayloadProperties(): array;

    /**
     * Consider if transition is available.
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Get the context.
     *
     * @return Context
     */
    public function getContext(): Context;

    /**
     * Validate the input.
     *
     * @param array $payload The payload.
     *
     * @return bool
     */
    public function validate(array $payload = []): bool;

    /**
     * Transit to next step.
     *
     * @throws WorkflowException For a workflow specific error.
     * @throws \Exception        For any error caused maybe by 3rd party code in the actions.
     *
     * @return State
     */
    public function transit(): State;
}
