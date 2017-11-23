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

namespace Netzmacht\Workflow\Flow\Exception;

use Netzmacht\Workflow\Exception\WorkflowException;

/**
 * Class FlowException.
 *
 * @package Netzmacht\Workflow\Flow\Exception
 */
class FlowException extends \RuntimeException implements WorkflowException
{

}
