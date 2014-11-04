<?php

namespace spec\Netzmacht\Workflow\Handler\Event;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class BuildFormEventSpec
 * @package spec\Netzmacht\Workflow\Handler\Event
 * @mixin PostTransitionEvent
 */
class PosTransitionEventSpec extends ObjectBehavior
{

    function let(Workflow $workflow, Item $item, Context $context, State $state)
    {
        $this->beConstructedWith($workflow, $item, $context, $state);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Event\PreTransitionEvent');
    }

    function it_gets_the_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_gets_the_context(Context $context)
    {
        $this->getContext()->shouldReturn($context);
    }

    function it_gets_the_item(Item $item)
    {
        $this->getItem()->shouldReturn($item);
    }

    function it_gets_current_state(State $state)
    {
        $this->getState()->shouldReturn($state);
    }
}
