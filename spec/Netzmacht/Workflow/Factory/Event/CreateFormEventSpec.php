<?php

namespace spec\Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Factory\Event\CreateFormEvent;
use Netzmacht\Workflow\Form\Form;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CreateFormEventSpec
 * @package spec\Netzmacht\Workflow\Factory\Event
 * @mixin CreateFormEvent
 */
class CreateFormEventSpec extends ObjectBehavior
{
    const TYPE = 'type';
    const NAME = 'name';

    function let()
    {
        $this->beConstructedWith(static::TYPE, static::NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\Event\CreateFormEvent');
    }

    function it_is_an_event()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\Event');
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn(static::TYPE);
    }

    function it_has_the_form(Form $form)
    {
        $this->setForm($form)->shouldReturn($this);
        $this->getForm()->shouldReturn($form);
    }
}
