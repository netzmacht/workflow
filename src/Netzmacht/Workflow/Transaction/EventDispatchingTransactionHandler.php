<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Transaction;


use Netzmacht\Workflow\Transaction\Event\TransactionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventBasedTransactionHandler implements is a transaction handler.
 *
 * It just dispatches events so that multiple transaction handler can subscribe to them.
 *
 * @package Netzmacht\Workflow\Transaction
 */
class EventDispatchingTransactionHandler implements TransactionHandler
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcherInterface $dispatcher The event dispatcher.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * Begin transaction fires an Events::TRANSACTION_BEGIN event.
     *
     * @return void
     */
    public function begin()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(TransactionEvent::TRANSACTION_BEGIN, $event);
    }

    /**
     * Begin transaction fires an Events::TRANSACTION_COMMIT event.
     *
     * @return void
     */
    public function commit()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(TransactionEvent::TRANSACTION_COMMIT, $event);
    }

    /**
     * Begin transaction fires an Events::TRANSACTION_ROLLBACK event.
     *
     * @return void
     */
    public function rollback()
    {
        $event = new TransactionEvent();
        $this->dispatcher->dispatch(TransactionEvent::TRANSACTION_ROLLBACK, $event);
    }
}
