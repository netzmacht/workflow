<?php

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\AbstractTransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class AbstractTransitionHandlerSpec
 * @package spec\Netzmacht\Workflow\Handler
 * @mixin TransitionHandler
 */
class AbstractTransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';

    const CONTEXT_CLASS = 'Netzmacht\Workflow\Flow\Context';
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';

    function let(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler
    ) {
        $this->beAnInstanceOf('spec\Netzmacht\Workflow\Handler\TransitionHandler');
        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\AbstractTransitionHandler');
    }

    function it_gets_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_gets_start_transition_if_not_started(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);
    }

    function it_gets_transition_if_already_started(Item $item, Workflow $workflow, Transition $transition)
    {
        $item->isWorkflowStarted()->willReturn(true);

        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);

    }

    function it_gets_item(Item $item)
    {
        $this->getItem()->shouldReturn($item);
    }

    function it_gets_form_after_validation(Form $form, Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->getForm()->shouldReturn(null);
        $this->validate($form);
        $this->getForm()->shouldReturn($form);
    }

    function it_gets_current_step_for_started_workflow(Item $item, Workflow $workflow, Step $step)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');

        $workflow->getStep('start')->willReturn($step);

        $this->getCurrentStep()->shouldReturn($step);
    }

    function it_gets_null_instead_of_step_if_not_started()
    {
        $this->getCurrentStep()->shouldBeNull();
    }

    function it_checks_if_workflow_is_started(Item $item)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $this->isWorkflowStarted()->shouldReturn(true);
    }

    function it_checks_if_workflow_is_not_started(Item $item)
    {
        $item->isWorkflowStarted()->willReturn(false);
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    function it_checks_if_input_data_is_required(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->requiresInputData()->willReturn(true);

        $this->requiresInputData()->shouldReturn(true);
    }
    function it_checks_if_input_data_is_not_required(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->requiresInputData()->willReturn(false);

        $this->requiresInputData()->shouldReturn(false);
    }

    function it_gets_the_context()
    {
        $this->getContext()->shouldHaveType(self::CONTEXT_CLASS);
    }

    function it_validates(Form $form, Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->buildForm($form, $item)->shouldBeCalled();
        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->requiresInputData()->willReturn(true);

        $form->validate(Argument::type(self::CONTEXT_CLASS))->shouldBeCalled()->willReturn(true);

        $this->validate($form)->shouldReturn(true);
    }

    function it_throws_during_transits_if_not_validated(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->shouldThrow('Netzmacht\Workflow\Flow\Exception\WorkflowException')->duringTransit();
    }

    function it_transits_to_next_state(
        Workflow $workflow,
        Transition $transition,
        Form $form,
        State $state,
        Item $item,
        Entity $entity
    ) {
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->start(
            Argument::type('Netzmacht\Workflow\Flow\Item'),
            Argument::type(self::CONTEXT_CLASS),
            Argument::type(self::ERROR_COLLECTION_CLASS)
        )
            ->willReturn($state);

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntity()->willReturn($entity);

        $this->validate($form);


        $this->transit()->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }
}

class TransitionHandler extends AbstractTransitionHandler
{
    protected function dispatchValidate(Form $form, $validated)
    {
        return $validated;
    }

    protected function dispatchPreTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    ) {
    }

    protected function dispatchPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        State $state
    ) {
    }

    protected function dispatchBuildForm(Form $form, Item $item, Context $context, $transitionName)
    {
    }
}
