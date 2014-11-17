<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ItemSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin Item
 */
class ItemSpec extends ObjectBehavior
{
    protected static $entity = array('id' => 5);

    function let(EntityId $entityId)
    {
        $this->beConstructedThrough('initialize', array($entityId, static::$entity));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

    function it_restores_state_history(EntityId $entityId, State $state)
    {
        $this->beConstructedThrough('reconstitute', array($entityId, static::$entity, array($state)));
    }

    function it_has_an_entity_id(EntityId $entityId)
    {
        $this->getEntityId()->shouldReturn($entityId);
    }

    function it_has_an_entity()
    {
        $this->getEntity()->shouldReturn(static::$entity);
    }

    function it_knows_if_workflow_is_started()
    {
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    function it_transits_to_a_successful_state(EntityId $entityId, State $state, State $newState, EntityId $entityId, Transition $transition, Context $context, ErrorCollection $errorCollection)
    {
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->willReturn('workflow_name');
        $state->isSuccessful()->willReturn(true);
        $state->transit(Argument::cetera())->willReturn($newState);

        $newState->getWorkflowName()->willReturn('workflow_name');
        $newState->getStepName()->willReturn('target');
        $newState->isSuccessful()->willReturn(true);

        $this->it_restores_state_history($entityId, $state);

        $this->transit($transition, $context, $errorCollection, true);

        $this->getCurrentStepName()->shouldReturn('target');
        $this->getWorkflowName()->shouldReturn('workflow_name');
        $this->getStateHistory()->shouldReturn(array($state, $newState));

        $this->getLatestState()->shouldHaveType('Netzmacht\Workflow\Flow\State');
        $this->getLatestState()->shouldNotBe($state);
    }


    function it_starts_a_new_workflow_state(
        EntityId $entityId,
        State $state,
        EntityId $entityId,
        Transition $transition,
        Workflow $workflow,
        Step $step,
        Context $context,
        ErrorCollection $errorCollection
    )
    {
        $transition->getWorkflow()->willReturn($workflow);
        $transition->getName()->willReturn('transition_name');
        $transition->getStepTo()->willReturn($step);

        $context->getProperties()->willReturn(array());
        $errorCollection->toArray()->willReturn(array());

        $this->beConstructedThrough('initialize', array($entityId, static::$entity));
        $this->start($transition, $context, $errorCollection, true)->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }

    function it_get_last_successful_state(EntityId $entityId, State $state, State $failedState)
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', array($entityId, static::$entity, array($state, $failedState)));

        $this->getCurrentStepName()->shouldReturn('start');
        $this->getLatestState()->shouldReturn($state);
    }

    function it_latest_state_from_history(EntityId $entityId, State $state, State $failedState)
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', array($entityId, static::$entity, array($state, $failedState)));

        $this->getLatestState(false)->shouldReturn($failedState);
    }
}
