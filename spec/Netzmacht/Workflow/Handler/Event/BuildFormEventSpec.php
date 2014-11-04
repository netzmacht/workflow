<?php

namespace spec\Netzmacht\Workflow\Handler\Event;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class BuildFormEventSpec
 * @package spec\Netzmacht\Workflow\Handler\Event
 * @mixin BuildFormEvent
 */
class BuildFormEventSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';

    function let(Form $form, Workflow $workflow, Item $item, Context $context)
    {
        $this->beConstructedWith($form, $workflow, $item, $context, static::TRANSITION_NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Event\BuildFormEvent');
    }

    function it_gets_the_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_gets_the_context(Context $context)
    {
        $this->getContext()->shouldReturn($context);
    }

    function it_gets_the_form(Form $form)
    {
        $this->getForm()->shouldReturn($form);
    }

    function it_gets_the_item(Item $item)
    {
        $this->getItem()->shouldReturn($item);
    }

    function it_gets_the_transition_name()
    {
        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }
}
