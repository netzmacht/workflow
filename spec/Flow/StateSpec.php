<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\Properties;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StateSpec
 * @package spec\Netzmacht\Workflow\Flow
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

    /**
     * @var EntityId
     */
    private $entityId;

    private static $errors = array(array('error.message', array()));

    function let(\DateTimeImmutable $dateTime)
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);

        $this->beConstructedWith(
            $this->entityId,
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
        $this->getReachedAt()->shouldBeAnInstanceOf(\DateTimeImmutable::class);
    }

    function it_stores_data()
    {
        $this->getData()->shouldReturn(static::$data);
    }

    function it_knows_entity_id()
    {
        $this->getEntityId()->shouldReturn($this->entityId);
    }

    function it_stores_error()
    {
        $this->getErrors()->shouldReturn(static::$errors);
    }

    function it_has_an_id()
    {
        $this->getStateId()->shouldReturn(static::STATE_ID);
    }

    function it_transits_to_next_state(
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) {
        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->getErrors()
            ->willReturn([]);

        $this->transit($transition, $context, false)
            ->shouldBeAnInstanceOf('Netzmacht\Workflow\Flow\State');
    }
}
