<?php

namespace spec\Netzmacht\Workflow\Factory;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory\EventDispatchingTransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingTransitionHandlerFactorySpec
 * @package spec\Netzmacht\Workflow\Factory
 * @mixin EventDispatchingTransitionHandlerFactory
 */
class EventDispatchingTransitionHandlerFactorySpec extends ObjectBehavior
{
    function let(EventDispatcher $eventDispatcher, TransactionHandler $transactionHandler, EntityManager $entityManager)
    {
        $this->beConstructedWith($entityManager, $transactionHandler, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\EventDispatchingTransitionHandlerFactory');
    }

    function it_creates_the_event_dispatching_transition_handler(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityManager $entityManager,
        EntityRepository $entityRepository
    )
    {
        $entityManager->getRepository('test')->willReturn($entityRepository);

        $this->createTransitionHandler(
            $item,
            $workflow,
            'start',
            'test',
            $stateRepository
        )
            ->shouldHaveType('Netzmacht\Workflow\Handler\EventDispatchingTransitionHandler');
    }
}
