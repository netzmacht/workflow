<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Transaction;

/**
 * Interface TransactionHandler describes the commonly used transaction steps begin, commit and rollback.
 */
interface TransactionHandler
{
    /**
     * Begin a transaction.
     */
    public function begin(): void;

    /**
     * Commit changes.
     */
    public function commit(): void;

    /**
     * Rollback the transaction.
     */
    public function rollback(): void;
}
