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
    public function begin();

    /**
     * Commit changes.
     *
     * @return void
     */
    public function commit();

    /**
     * Rollback the transaction.
     *
     * @return void
     */
    public function rollback();
}
