<?php

namespace spec\Netzmacht\Workflow\Handler\Event;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\Event\AbstractFlowEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class AbstractFlowEventSpec
 * @package spec\Netzmacht\Workflow\Handler\Event
 * @mixin FlowEvent
 */
class AbstractFlowEventSpec extends ObjectBehavior
{
    function let(Workflow $workflow, Item $item, Context $context)
    {
        $this->beAnInstanceOf('spec\Netzmacht\Workflow\Handler\Event\FlowEvent');
        $this->beConstructedWith($workflow, $item, $context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Event\AbstractFlowEvent');
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
}

class FlowEvent extends AbstractFlowEvent
{

}
