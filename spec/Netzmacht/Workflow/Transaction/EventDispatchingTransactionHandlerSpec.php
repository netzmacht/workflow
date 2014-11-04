<?php

namespace spec\Netzmacht\Workflow\Transaction;

use Netzmacht\Workflow\Transaction\Event\TransactionEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatchingTransactionHandlerSpec extends ObjectBehavior
{
    const TRANSACTION_EVENT_CLASS = 'Netzmacht\Workflow\Transaction\Event\TransactionEvent';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Transaction\EventDispatchingTransactionHandler');
        $this->shouldImplement('Netzmacht\Workflow\Transaction\TransactionHandler');
    }

    function let(EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($eventDispatcher);
    }

    function it_begins_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                TransactionEvent::TRANSACTION_BEGIN,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->begin();
    }

    function it_commits_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                TransactionEvent::TRANSACTION_COMMIT,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->commit();
    }

    function it_rollbacks_a_transaction(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher
            ->dispatch(
                TransactionEvent::TRANSACTION_ROLLBACK,
                Argument::type(self::TRANSACTION_EVENT_CLASS)
            )
            ->shouldBeCalled();

        $this->rollback();
    }
}
