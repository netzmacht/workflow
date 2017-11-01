<?php

namespace spec\Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Manager\Manager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CreateManagerEventSpec
 * @package spec\Netzmacht\Workflow\Factory\Event
 * @mixin CreateManagerEvent
 */
class CreateManagerEventSpec extends ObjectBehavior
{
    const TYPE = 'type';
    const PROVIDER = 'provider';

    function let()
    {
        $this->beConstructedWith(static::PROVIDER, static::TYPE);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\Event\CreateManagerEvent');
    }

    function it_is_an_event()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\Event');
    }

    function it_has_a_provider()
    {
        $this->getProviderName()->shouldReturn(static::PROVIDER);
    }

    function it_accepts_a_type()
    {
        $this->getWorkflowType()->shouldReturn(static::TYPE);
    }

    function it_does_not_require_a_type()
    {
        $this->beConstructedWith(static::PROVIDER);
        $this->getWorkflowType()->shouldReturn(null);
    }

    function it_sets_the_manager(Manager $manager)
    {
        $this->setManager($manager)->shouldReturn($this);
        $this->getManager()->shouldReturn($manager);
    }
}
