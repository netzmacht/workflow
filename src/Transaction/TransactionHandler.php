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
 * Interface TransactionHandler describes the commonly used transaction steps begin, commit and rollback.
 *
 * @package Netzmacht\Workflow\Transaction
 */
interface TransactionHandler
{
    /**
     * Begin a transaction.
     *
     * @return void
     */
    public function begin(): void;

    /**
     * Commit changes.
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Rollback the transaction.
     *
     * @return void
     */
    public function rollback(): void;
}
