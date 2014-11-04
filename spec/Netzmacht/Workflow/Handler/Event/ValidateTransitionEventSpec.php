<?php

namespace spec\Netzmacht\Workflow\Handler\Event;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class BuildFormEventSpec
 * @package spec\Netzmacht\Workflow\Handler\Event
 * @mixin ValidateTransitionEvent
 */
class ValidateTransitionEventSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';

    function let(Workflow $workflow, Item $item, Context $context, Form $form)
    {
        $this->beConstructedWith($workflow, static::TRANSITION_NAME, $item, $context, $form, true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent');
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

    function it_knows_validation_state()
    {
        $this->isValid()->shouldReturn(true);
    }

    function it_changes_validation_state()
    {
        $this->isValid()->shouldReturn(true);
        $this->setInvalid()->shouldReturn($this);
        $this->isValid()->shouldReturn(false);
    }
}
