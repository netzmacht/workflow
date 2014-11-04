<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Workflow
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
     * @var \Netzmacht\Workflow\Flow\Workflow
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
     * The entity repository.
     *
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * The state repository.
     *
     * @var \Netzmacht\Workflow\Data\StateRepository
     */
    private $stateRepository;

    /**
     * The transaction handler.
     *
     * @var \Netzmacht\Workflow\Transaction\TransactionHandler
     */
    private $transactionHandler;

    /**
     * The transition context.
     *
     * @var \Netzmacht\Workflow\Flow\Context
     */
    private $context;


    /**
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     * @param Context            $context            The context of the transition.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Context $context
    ) {
        $this->item               = $item;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->entityRepository   = $entityRepository;
        $this->stateRepository    = $stateRepository;
        $this->transactionHandler = $transactionHandler;
        $this->context            = $context;
    }


    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get the input form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get the transition.
     *
     * @return \Netzmacht\Workflow\Flow\Transition
     *
     * @throws TransitionNotFoundException If transition was not found.
     */
    public function getTransition()
    {
        if ($this->isWorkflowStarted()) {
            return $this->workflow->getTransition($this->transitionName);
        }

        return $this->workflow->getStartTransition();
    }

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return Step|null
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
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted()
    {
        return $this->item->isWorkflowStarted();
    }

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        return $this->getTransition()->requiresInputData();
    }

    /**
     * Get the context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Validate the input.
     *
     * @param \Netzmacht\Workflow\Form\Form $form The transition form.
     *
     * @return bool
     */
    public function validate(Form $form)
    {
        $this->buildForm($form);

        if (!$this->validated) {
            if ($this->requiresInputData()) {
                $this->validated = $this->getForm()->validate($this->context);
            } else {
                $this->validated = true;
            }
        }

        return $this->dispatchValidate($form, $this->validated);
    }

    /**
     * Transit to next step.
     *
     * @throws WorkflowException If an invalid transition was requested.
     * @throws \Exception        If some actions throws an unknown exception.
     *
     * @return State
     */
    public function transit()
    {
        $this->guardValidated();

        // it's a start transition.
        if (!$this->isWorkflowStarted()) {
            return $this->start();
        }

        $this->guardAllowedTransition($this->transitionName);
        $transitionName = $this->transitionName;

        return $this->doStateTransition(
            function (Workflow $workflow, Item $item, Context $context) use ($transitionName) {
                return $workflow->transit($item, $transitionName, $context);
            }
        );
    }

    /**
     * Start a transition.
     *
     * @return \Netzmacht\Workflow\Flow\State
     *
     * @throws WorkflowException If an invalid transition was requested.
     * @throws \Exception        If some actions throws an unknown exception.
     */
    private function start()
    {
        return $this->doStateTransition(
            function (Workflow $workflow, Item $item, Context $context) {
                return $workflow->start($item, $context);
            }
        );
    }

    /**
     * Execute a state transition. Transition will be handled as an transaction.
     *
     * @param callable $processor The processor being called to transist.
     *
     * @return State
     *
     * @throws WorkflowException If an invalid transition was requested.
     * @throws \Exception        If some actions throws an unknown exception.
     */
    private function doStateTransition($processor)
    {
        $this->transactionHandler->begin();

        try {
            $this->dispatchPreTransit($this->workflow, $this->item, $this->context, $this->getTransition()->getName());

            $state = call_user_func($processor, $this->workflow, $this->item, $this->context);

            $this->dispatchPostTransit($this->workflow, $this->item, $this->context, $state);

            $this->stateRepository->add($state);
            $this->entityRepository->add($this->item->getEntity());
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        return $state;
    }

    /**
     * @param Form $form
     */
    private function buildForm(Form $form)
    {
        $this->form = $form;
        $this->getTransition()->buildForm($this->form, $this->item);

        $this->dispatchBuildForm($form, $this->item, $this->context, $this->getTransition()->getName());
    }

    /**
     * Guard that transition was validated before.
     *
     * @throws WorkflowException If transition.
     *
     * @return void
     */
    private function guardValidated()
    {
        if ($this->validated === null) {
            throw new WorkflowException('Transition was not validated so far.');
        } elseif (!$this->validated) {
            throw new WorkflowException('Transition is in a invalid state and can\'t be processed.');
        }
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
            throw new WorkflowException(
                sprintf(
                    'Not allowed to process transition "%s". Workflow "%s" not started for item "%s"',
                    $transitionName,
                    $this->workflow->getName(),
                    $this->item->getEntity()->getEntityId()
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

    /**
     * Consider if form is validated.
     *
     * @param Form $form      Transition form.
     * @param bool $validated Current validation state
     *
     * @return bool
     */
    abstract protected function dispatchValidate(Form $form, $validated);

    /**
     * Dispatch pre transition.
     *
     * @param Workflow $workflow       The workflow.
     * @param Item     $item           Current workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     *
     * @return void
     */
    abstract protected function dispatchPreTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    );

    /**
     * Dispatch post transition.
     *
     * @param Workflow $workflow The workflow.
     * @param Item     $item     Current workflow item.
     * @param Context  $context  Transition context.
     * @param          $state
     * @return void
     */
    abstract protected function dispatchPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $state
    );

    /**
     * Dispatch build form.
     *
     * @param Form    $form Form being build.
     * @param Item    $item Workflow item.
     * @param Context $context
     * @param string  $transitionName
     *
     * @return void
     */
    abstract protected function dispatchBuildForm(Form $form, Item $item, Context $context, $transitionName);
}
