<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;

class RepositoryBasedTransitionHandlerFactorySpec extends ObjectBehavior
{
    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    public function let(TransactionHandler $transactionHandler, EntityManager $entityManager): void
    {
        $this->beConstructedWith($entityManager, $transactionHandler);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandlerFactory');
    }

    public function it_gets_entity_manager(EntityManager $entityManager): void
    {
        $this->getEntityManager()->shouldReturn($entityManager);
    }

    public function it_gets_transaction_handler(TransactionHandler $transactionHandler): void
    {
        $this->getTransactionHandler()->shouldReturn($transactionHandler);
    }

    public function it_creates_the_repository_based_transition_handler(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityManager $entityManager,
        EntityRepository $entityRepository,
    ): void {
        $entityManager->getRepository('test')->willReturn($entityRepository);

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntity()->willReturn(static::$entity);

        $this->createTransitionHandler(
            $item,
            $workflow,
            null,
            'test',
            $stateRepository,
        )
            ->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler');
    }
}
