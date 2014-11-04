<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ItemSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin Item
 */
class ItemSpec extends ObjectBehavior
{
    function let(Entity $entity)
    {
        $this->beConstructedThrough('start', array($entity));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

    function it_restores_state_history(Entity $entity, State $state)
    {
        $this->beConstructedThrough('reconstitute', array($entity, array($state)));
    }

    function it_has_an_entity(Entity $entity)
    {
        $this->getEntity()->shouldReturn($entity);
    }

    function it_knows_if_workflow_is_started()
    {
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    function it_transits_to_a_successful_state(Entity $entity, State $state, EntityId $entityId)
    {
        $state->getStateId()->willReturn(1);
        $state->getEntityId()->willReturn($entityId);
        $state->getErrors()->willReturn(array());
        $state->getData()->willReturn(array());
        $state->getReachedAt()->willReturn(new \DateTime());
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->willReturn('workflow_name');
        $state->getTransitionName()->willReturn('transition_name');
        $state->isSuccessful()->willReturn(true);

        $this->it_restores_state_history($entity, $state);

        $this->getCurrentStepName()->shouldReturn('start');
        $this->getStateHistory()->shouldReturn(array($state));
        $this->getWorkflowName()->shouldReturn('workflow_name');
        $this->getLatestState()->shouldReturn($state);
    }

    function it_get_last_successful_state(Entity $entity, State $state, State $failedState)
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', array($entity, array($failedState, $state)));

        $this->getCurrentStepName()->shouldReturn('start');
        $this->getLatestState()->shouldReturn($state);
    }

    function it_latest_state_from_history(Entity $entity, State $state, State $failedState)
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', array($entity, array($state, $failedState)));

        $this->getLatestState(false)->shouldReturn($failedState);
    }
}
