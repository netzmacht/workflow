<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Transaction;

use Override;

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

    #[Override]
    public function begin(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->begin();
        }
    }

    #[Override]
    public function commit(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->commit();
        }
    }

    #[Override]
    public function rollback(): void
    {
        foreach ($this->transactionHandlers as $handler) {
            $handler->rollback();
        }
    }
}
