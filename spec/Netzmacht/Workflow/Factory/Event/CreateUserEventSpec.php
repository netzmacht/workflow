<?php

namespace spec\Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Security\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CreateUserEventSpec
 * @package spec\Netzmacht\Workflow\Factory\Event
 * @mixin CreateUserEvent
 */
class CreateUserEventSpec extends ObjectBehavior
{
    function let(User $user)
    {
        $this->beConstructedWith($user);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\Event\CreateUserEvent');
    }

    function it_is_an_event()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\Event');
    }

    function it_has_the_user(User $user)
    {
        $this->getUser()->shouldReturn($user);
    }
}
