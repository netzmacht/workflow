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
