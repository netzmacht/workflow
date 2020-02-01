<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Manager\WorkflowManager;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;

/**
 * Class EventDispatchingTransitionHandlerFactorySpec
 *
 * @package spec\Netzmacht\Workflow\Factory
 */
class RepositoryBasedTransitionHandlerFactorySpec extends ObjectBehavior
{
    protected static $entity = ['id' => 5];

    function let(TransactionHandler $transactionHandler, EntityManager $entityManager)
    {
        $this->beConstructedWith($entityManager, $transactionHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandlerFactory');
    }

    function it_gets_entity_manager(EntityManager $entityManager)
    {
        $this->getEntityManager()->shouldReturn($entityManager);
    }

    function it_gets_transaction_handler(TransactionHandler $transactionHandler)
    {
        $this->getTransactionHandler()->shouldReturn($transactionHandler);
    }

    function it_creates_the_repository_based_transition_handler(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityManager $entityManager,
        EntityRepository $entityRepository,
        WorkflowManager $workflowManager,
        Transition $startTransition
    ) {
        $entityManager->getRepository('test')->willReturn($entityRepository);

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntity()->willReturn(static::$entity);

        $workflow->getStartTransition()->willReturn($startTransition);

        $this->createTransitionHandler(
            $item,
            $workflow,
            null,
            'test',
            $stateRepository,
            $workflowManager
        )
            ->shouldHaveType('Netzmacht\Workflow\Handler\ChangeWorkflowTransitionHandler');
    }
}
