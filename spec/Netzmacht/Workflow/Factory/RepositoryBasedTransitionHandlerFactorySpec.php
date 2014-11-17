<?php

namespace spec\Netzmacht\Workflow\Factory;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory\RepositoryBasedTransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\Listener;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EventDispatchingTransitionHandlerFactorySpec
 * @package spec\Netzmacht\Workflow\Factory
 * @mixin RepositoryBasedTransitionHandlerFactory
 */
class RepositoryBasedTransitionHandlerFactorySpec extends ObjectBehavior
{
    protected static $entity = array('id' => 5);

    function let(Listener $dispatcher, TransactionHandler $transactionHandler, EntityManager $entityManager)
    {
        $this->beConstructedWith($entityManager, $transactionHandler, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\RepositoryBasedTransitionHandlerFactory');
    }

    function it_gets_entity_manager(EntityManager $entityManager)
    {
        $this->getEntityManager()->shouldReturn($entityManager);
    }

    function it_gets_transaction_handler(TransactionHandler $transactionHandler)
    {
        $this->getTransactionHandler()->shouldReturn($transactionHandler);
    }

    function it_creates_the_event_dispatching_transition_handler(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityManager $entityManager,
        EntityRepository $entityRepository
    ) {
        $entityManager->getRepository('test')->willReturn($entityRepository);

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntity()->willReturn(static::$entity);

        $this->createTransitionHandler(
            $item,
            $workflow,
            null,
            'test',
            $stateRepository
        )
            ->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler');
    }
}
