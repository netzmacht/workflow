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
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * AbstractTransitionHandler can be used as base class for transition handler implementations.
 *
 * @package Netzmacht\Workflow\Handler
 */
abstract class AbstractTransitionHandler implements TransitionHandler
{
    /**
     * The given entity.
     *
     * @var Item
     */
    private $item;

    /**
     * The current workflow.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * The transition name which will be handled.
     *
     * @var string
     */
    private $transitionName;

    /**
     * Validation state.
     *
     * @var bool
     */
    private $validated;

    /**
     * The transaction handler.
     *
     * @var TransactionHandler
     */
    protected $transactionHandler;

    /**
     * The transition context.
     *
     * @var Context
     */
    private $context;

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
        $transitionName,
        TransactionHandler $transactionHandler
    ) {
        $this->item               = $item;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->transactionHandler = $transactionHandler;
        $this->context            = new Context();

        $this->guardAllowedTransition($transitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransition(): Transition
    {
        if ($this->isWorkflowStarted()) {
            return $this->workflow->getTransition($this->transitionName);
        }

        return $this->workflow->getStartTransition();
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function isWorkflowStarted(): bool
    {
        return $this->item->isWorkflowStarted();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPayloadProperties(): array
    {
        return $this->getTransition()->getRequiredPayloadProperties($this->item);
    }

    /**
     * Consider if transition is available.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->getTransition()->isAvailable($this->item, $this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep():? Step
    {
        if ($this->isWorkflowStarted()) {
            $stepName = $this->item->getCurrentStepName();

            return $this->workflow->getStep($stepName);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
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
        if (!$transition->validate($this->item, $this->context)) {
            $this->validated = false;
        }

        if ($this->validated && !$transition->checkCondition($this->item, $this->context)) {
            $this->validated = false;
        }

        return $this->validated;
    }

    /**
     * Execute the transition.
     *
     * @return State
     */
    protected function executeTransition(): State
    {
        $transition = $this->getTransition();
        $success    = $transition->executeActions($this->item, $this->context);

        if ($this->isWorkflowStarted()) {
            $state = $this->getItem()->transit($transition, $this->context, $success);
        } else {
            $state = $this->getItem()->start($transition, $this->context, $success);
        }

        $transition->executePostActions($this->item, $this->context);

        return $state;
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws FlowException If transition.
     *
     * @return void
     */
    protected function guardValidated(): void
    {
        if ($this->validated === null) {
            throw new FlowException('Transition was not validated so far.');
        } elseif (!$this->validated) {
            throw new FlowException('Transition is in a invalid state and can\'t be processed.');
        }
    }

    /**
     * Guard that requested transition is allowed.
     *
     * @param string|null $transitionName Transition to be processed.
     *
     * @throws FlowException If Transition is not allowed.
     *
     * @return void
     */
    private function guardAllowedTransition(?string $transitionName): void
    {
        if (!$this->isWorkflowStarted()) {
            if ($transitionName === null || $transitionName === $this->getWorkflow()->getStartTransition()->getName()) {
                return;
            }

            throw new FlowException(
                sprintf(
                    'Not allowed to process transition "%s". Workflow "%s" not started for item "%s"',
                    $transitionName,
                    $this->workflow->getName(),
                    $this->item->getEntityId()
                )
            );
        }

        $step = $this->getCurrentStep();

        if (!$step->isTransitionAllowed($transitionName)) {
            throw new FlowException(
                sprintf(
                    'Not allowed to process transition "%s". Transition is not allowed in step "%s"',
                    $transitionName,
                    $step->getName()
                )
            );
        }
    }
}
