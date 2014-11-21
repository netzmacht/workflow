<?php

namespace spec\Netzmacht\Workflow;

use Netzmacht\Workflow\Factory;
use Netzmacht\Workflow\Factory\Event\CreateFormEvent;
use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Manager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class FactorySpec
 * @package spec\Netzmacht\Workflow
 * @mixin Factory
 */
class FactorySpec extends ObjectBehavior
{
    function let(EventDispatcher $eventDispatcher)
    {
        $this->beConstructedWith($eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory');
    }

    function it_creates_manager(EventDispatcher $eventDispatcher, Manager $manager)
    {
        $eventDispatcher->dispatch(
            CreateManagerEvent::NAME,
            Argument::type('Netzmacht\Workflow\Factory\Event\CreateManagerEvent')
        )->will(
            function ($arguments) use ($manager) {
                /** @var CreateManagerEvent $event */
                $event = $arguments[1];
                $event->setManager($manager->getWrappedObject());
            }
        );

        $this->createManager('provider_name', 'type_name')->shouldReturn($manager);
    }

    function it_throws_if_no_manager_is_created(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->dispatch(
            CreateManagerEvent::NAME,
            Argument::type('Netzmacht\Workflow\Factory\Event\CreateManagerEvent')
        )->shouldBeCalled();

        $this->shouldThrow('RuntimeException')->duringCreateManager('provider_name', 'type_name');
    }

    function it_creates_form(EventDispatcher $eventDispatcher, Form $form)
    {
        $eventDispatcher->dispatch(
            CreateFormEvent::NAME,
            Argument::type('Netzmacht\Workflow\Factory\Event\CreateFormEvent')
        )->will(
            function ($arguments) use ($form) {
                /** @var CreateFormEvent $event */
                $event = $arguments[1];
                $event->setForm($form->getWrappedObject());
            }
        );

        $this->createForm('form_type')->shouldReturn($form);
    }

    function it_throws_if_no_form_is_created(EventDispatcher $eventDispatcher)
    {
        $eventDispatcher->dispatch(
            CreateFormEvent::NAME,
            Argument::type('Netzmacht\Workflow\Factory\Event\CreateFormEvent')
        )->shouldBeCalled();

        $this->shouldThrow('RuntimeException')->duringCreateForm('form_type');
    }
}
