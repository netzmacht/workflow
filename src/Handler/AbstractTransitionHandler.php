<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Override;

use function sprintf;

/**
 * AbstractTransitionHandler can be used as base class for transition handler implementations.
 */
abstract class AbstractTransitionHandler implements TransitionHandler
{
    /**
     * The given entity.
     */
    private Item $item;

    /**
     * The current workflow.
     */
    private Workflow $workflow;

    /**
     * The transition name which will be handled.
     */
    private string $transitionName;

    /**
     * Validation state.
     */
    private bool $validated;

    /**
     * The transaction handler.
     */
    protected TransactionHandler $transactionHandler;

    /**
     * The transition context.
     */
    private Context $context;

    /**
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     *
     * @throws FlowException If invalid transition name is given.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        string $transitionName,
        TransactionHandler $transactionHandler,
    ) {
        $this->item               = $item;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->transactionHandler = $transactionHandler;
        $this->context            = new Context();

        $this->guardAllowedTransition($transitionName);
    }

    #[Override]
    public function getTransition(): Transition
    {
        if ($this->isWorkflowStarted()) {
            return $this->workflow->getTransition($this->transitionName);
        }

        return $this->workflow->getStartTransition();
    }

    #[Override]
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    #[Override]
    public function getItem(): Item
    {
        return $this->item;
    }

    #[Override]
    public function getContext(): Context
    {
        return $this->context;
    }

    #[Override]
    public function isWorkflowStarted(): bool
    {
        return $this->item->isWorkflowStarted();
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getRequiredPayloadProperties(): array
    {
        return $this->getTransition()->getRequiredPayloadProperties($this->item);
    }

    /**
     * Consider if transition is available.
     */
    #[Override]
    public function isAvailable(): bool
    {
        return $this->getTransition()->isAvailable($this->item, $this->context);
    }

    #[Override]
    public function getCurrentStep(): Step|null
    {
        if ($this->isWorkflowStarted()) {
            $stepName = $this->item->getCurrentStepName();

            return $this->workflow->getStep($stepName);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function validate(array $payload = []): bool
    {
        // first build the form
        $this->context   = $this->context->createCleanCopy($payload);
        $this->validated = false;
        $transition      = $this->getTransition();

        // check pre conditions first
        if ($transition->checkPreCondition($this->item, $this->context)) {
            $this->validated = true;
        }

        // Validate the actions
        if (! $transition->validate($this->item, $this->context)) {
            $this->validated = false;
        }

        if ($this->validated && ! $transition->checkCondition($this->item, $this->context)) {
            $this->validated = false;
        }

        return $this->validated;
    }

    /**
     * Execute the transition.
     */
    protected function executeTransition(): State
    {
        return $this->getTransition()->execute($this->item, $this->context);
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws FlowException If transition.
     */
    protected function guardValidated(): void
    {
        if ($this->validated === null) {
            throw new FlowException('Transition was not validated so far.');
        }

        if (! $this->validated) {
            throw new FlowException('Transition is in a invalid state and can\'t be processed.');
        }
    }

    /**
     * Guard that requested transition is allowed.
     *
     * @param string|null $transitionName Transition to be processed.
     *
     * @throws FlowException If Transition is not allowed.
     */
    private function guardAllowedTransition(string|null $transitionName): void
    {
        if (! $this->isWorkflowStarted()) {
            if ($transitionName === null || $transitionName === $this->getWorkflow()->getStartTransition()->getName()) {
                return;
            }

            throw new FlowException(
                sprintf(
                    'Not allowed to process transition "%s". Workflow "%s" not started for item "%s"',
                    $transitionName,
                    $this->workflow->getName(),
                    $this->item->getEntityId(),
                ),
            );
        }

        $step = $this->getCurrentStep();

        if (! $step->isTransitionAllowed($transitionName)) {
            throw new FlowException(
                sprintf(
                    'Not allowed to process transition "%s". Transition is not allowed in step "%s"',
                    $transitionName,
                    $step->getName(),
                ),
            );
        }
    }
}
