<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
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
     * The form object for user input.
     *
     * @var Form
     */
    private $form;

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
     * Error collection of errors occurred during transition handling.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Transition handler listener.
     *
     * @var Listener
     */
    protected $listener;

    /**
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     * @param Listener           $listener           Transition handler dispatcher.
     *
     * @throws WorkflowException If invalid transition name is given.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        TransactionHandler $transactionHandler,
        Listener $listener
    ) {
        $this->item               = $item;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->transactionHandler = $transactionHandler;
        $this->context            = new Context();
        $this->errorCollection    = new ErrorCollection();
        $this->listener           = $listener;

        $this->guardAllowedTransition($transitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function getTransition()
    {
        if ($this->isWorkflowStarted()) {
            return $this->workflow->getTransition($this->transitionName);
        }

        return $this->workflow->getStartTransition();
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function isWorkflowStarted()
    {
        return $this->item->isWorkflowStarted();
    }

    /**
     * {@inheritdoc}
     */
    public function isInputRequired()
    {
        return $this->getTransition()->isInputRequired($this->item);
    }

    /**
     * Consider if transition is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getTransition()->isAvailable($this->item, $this->context, $this->errorCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
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
    public function validate(Form $form)
    {
        // first build the form
        $this->errorCollection->reset();
        $this->buildForm($form);
        $this->validated = false;

        // check pre conditions first
        if ($this->getTransition()->checkPreCondition($this->item, $this->context, $this->errorCollection)) {
            $this->validated = true;

            // validate form input now
            if ($this->isInputRequired($this->item)) {
                $this->validated = $this->getForm()->validate();

                if (!$this->validated) {
                    $this->errorCollection->addError(
                        'transition.validate.form.failed',
                        array(),
                        $form->getErrorCollection()
                    );
                }
            }

            // check conditions after validating the form so that context is setup
            if ($this->validated &&
                !$this->getTransition()->checkCondition($this->item, $this->context, $this->errorCollection)
            ) {
                $this->validated = false;
            }
        }

        // trigger the listener, no matter if validated so far
        $this->validated = $this->listener->onValidate(
            $form,
            $this->validated,
            $this->workflow,
            $this->item,
            $this->context,
            $this->getTransition()->getName()
        );

        return $this->validated;
    }

    /**
     * Execute the transition.
     *
     * @return State
     */
    protected function executeTransition()
    {
        $transition = $this->getTransition();
        $success    = $transition->executeActions($this->item, $this->context, $this->errorCollection);

        if ($this->isWorkflowStarted()) {
            return $this->getItem()->transit($transition, $this->context, $this->errorCollection, $success);
        }

        return $this->getItem()->start($transition, $this->context, $this->errorCollection, $success);
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws WorkflowException If transition.
     *
     * @return void
     */
    protected function guardValidated()
    {
        if ($this->validated === null) {
            throw new WorkflowException('Transition was not validated so far.');
        } elseif (!$this->validated) {
            throw new WorkflowException('Transition is in a invalid state and can\'t be processed.');
        }
    }

    /**
     * Build transition form.
     *
     * @param Form $form The form being built.
     *
     * @return void
     */
    private function buildForm(Form $form)
    {
        $this->form = $form;
        $this->getTransition()->buildForm($this->form, $this->item);
        $form->prepare($this->item, $this->context);

        $this->listener->onBuildForm(
            $form,
            $this->workflow,
            $this->item,
            $this->context,
            $this->getTransition()->getName()
        );
    }

    /**
     * Guard that requested transition is allowed.
     *
     * @param string $transitionName Transition to be processed.
     *
     * @throws WorkflowException If Transition is not allowed.
     *
     * @return void
     */
    private function guardAllowedTransition($transitionName)
    {
        if (!$this->isWorkflowStarted()) {
            if (!$transitionName || $transitionName === $this->getWorkflow()->getStartTransition()->getName()) {
                return;
            }

            throw new WorkflowException(
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
            throw new WorkflowException(
                sprintf(
                    'Not allowed to process transition "%s". Transition is not allowed in step "%s"',
                    $transitionName,
                    $step->getName()
                )
            );
        }
    }
}
