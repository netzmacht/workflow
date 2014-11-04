<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Transaction\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class TransactionEvent is dispatched by the event based transaction handler.
 *
 * @package Netzmacht\Workflow\Event
 */
class TransactionEvent extends Event
{
    const TRANSACTION_BEGIN = 'workflow.transaction.begin';

    const TRANSACTION_COMMIT = 'workflow.transaction.commit';

    const TRANSACTION_ROLLBACK = 'workflow.transaction.rollback';
}
