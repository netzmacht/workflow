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

declare(strict_types=1);

namespace Netzmacht\Workflow\Transaction;

/**
 * Class DelegatingTransactionHandler delegates transaction commands to its children handlers.
 *
 * @package Netzmacht\Workflow\Transaction
 */
class DelegatingTransactionHandler implements TransactionHandler
{
    /**
     * Transaction handler.
     *
     * @var TransactionHandler[]
     */
    private $transactionHandlers;

    /**
     * DelegatingTransactionHandler constructor.
     *
     * @param TransactionHandler[] $transactionHandlers Child transaction handlers.
     */
    public function __construct(array $transactionHandlers)
    {
        $this->transactionHandlers = $transactionHandlers;
    }

    /**
     * {@inheritdoc}
     */
    public function begin(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->begin();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->commit();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->rollback();
        }
    }
}
