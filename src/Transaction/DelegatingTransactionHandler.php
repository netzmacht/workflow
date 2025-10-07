<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Transaction;

/**
 * Class DelegatingTransactionHandler delegates transaction commands to its children handlers.
 */
class DelegatingTransactionHandler implements TransactionHandler
{
    /**
     * Transaction handler.
     *
     * @var TransactionHandler[]
     */
    private array $transactionHandlers;

    /** @param TransactionHandler[] $transactionHandlers Child transaction handlers. */
    public function __construct(array $transactionHandlers)
    {
        $this->transactionHandlers = $transactionHandlers;
    }

    public function begin(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->begin();
        }
    }

    public function commit(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->commit();
        }
    }

    public function rollback(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->rollback();
        }
    }
}
