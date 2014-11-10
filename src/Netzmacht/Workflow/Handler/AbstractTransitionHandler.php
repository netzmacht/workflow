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
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
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
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler
    ) {
        $this->item               = $item;
        $this->workflow           = $workflow;
        $this->transitionName     = $transitionName;
        $this->entityRepository   = $entityRepository;
        $this->stateRepository    = $stateRepository;
        $this->transactionHandler = $transactionHandler;
        $this->context            = new Context();
        $this->errorCollection    = new ErrorCollection();
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
    public function isWorkflowStarted()
    {
        return $this->item->isWorkflowStarted();
    }

    /**
     * {@inheritdoc}
     */
    public function requiresInputData()
    {
        return $this->getTransition()->requiresInputData();
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
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Form $form)
    {
        $this->buildForm($form);

        if (!$this->validated) {
            if ($this->requiresInputData()) {
                $this->validated = $this->getForm()->validate($this->context);

                if (!$this->validated) {
                    $this->errorCollection->addError(
                        'transition.validate.form.failed',
                        array(),
                        $form->getErrorCollection()
                    );
                }
            } else {
                $this->validated = true;
            }
        }

        return $this->dispatchValidate($form, $this->validated);
    }

    /**
     * {@inheritdoc}
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
            function (
                Workflow $workflow,
                Item $item,
                Context $context,
                ErrorCollection $errorCollection
            ) use ($transitionName) {
                return $workflow->transit($item, $transitionName, $context, $errorCollection);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    private function start()
    {
        return $this->doStateTransition(
            function (Workflow $workflow, Item $item, Context $context, ErrorCollection $errorCollection) {
                return $workflow->start($item, $context, $errorCollection);
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

            $state = call_user_func($processor, $this->workflow, $this->item, $this->context, $this->errorCollection);

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
     * @param bool $validated Current validation state.
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
     * @param State    $state    Item state.
     *
     * @return void
     */
    abstract protected function dispatchPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        State $state
    );

    /**
     * Dispatch build form.
     *
     * @param Form    $form           Form being build.
     * @param Item    $item           Workflow item.
     * @param Context $context        Transition context.
     * @param string  $transitionName Transition name.
     *
     * @return void
     */
    abstract protected function dispatchBuildForm(Form $form, Item $item, Context $context, $transitionName);
}
