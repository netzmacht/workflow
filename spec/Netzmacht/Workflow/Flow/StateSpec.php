<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StateSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin State
 */
class StateSpec extends ObjectBehavior
{
    const WORKFLOW_NAME = 'workflow_name';
    const TRANSITION_NAME = 'transition_name';
    const STEP_TO = 'step_to';
    const STATE_ID = 121;

    private static $data = array(
        'foo' => true,
        'bar' => false
    );

    private static $errors = array(array('error.message', array()));

    function let(EntityId $entityId, \DateTime $dateTime)
    {
        $this->beConstructedWith(
            $entityId,
            static::WORKFLOW_NAME,
            static::TRANSITION_NAME,
            static::STEP_TO,
            true,
            static::$data,
            $dateTime,
            static::$errors,
            static::STATE_ID
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }

    function it_knows_current_step()
    {
        $this->getStepName()->shouldReturn(static::STEP_TO);
    }

    function it_knows_last_transition()
    {
        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }

    function it_knows_reached_time()
    {
        $this->getReachedAt()->shouldBeAnInstanceOf('DateTime');
    }

    function it_stores_data()
    {
        $this->getData()->shouldReturn(static::$data);
    }

    function it_knows_entity_id(EntityId $entityId)
    {
        $this->getEntityId()->shouldReturn($entityId);
    }

    function it_stores_error()
    {
        $this->getErrors()->shouldReturn(static::$errors);
    }

    function it_has_an_id()
    {
        $this->getStateId()->shouldReturn(static::STATE_ID);
    }

    function it_transits_to_next_state(Transition $transition, Context $context, ErrorCollection $errorCollection)
    {
        $context->getProperties()->willReturn(array());
        $context->getErrorCollection()->willReturn($errorCollection);
        $errorCollection->getErrors()->willReturn(array());

        $this->transit($transition, $context, false)->shouldBeAnInstanceOf('Netzmacht\Workflow\Flow\State');
    }
}
