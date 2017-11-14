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

namespace Netzmacht\Workflow\Exception;

use Exception;
use Netzmacht\Workflow\Data\EntityId;

/**
 * Class WorkflowNotFound
 *
 * @package Netzmacht\Workflow\Exception
 */
class WorkflowNotFound extends \RuntimeException implements WorkflowException
{
    /**
     * Create exception with the workflow name.
     *
     * @param string    $workflowName Current workflow name.
     * @param int       $code         Error code.
     * @param Exception $previous     Previous thrown exception.
     *
     * @return self
     */
    public static function withName(string $workflowName, int $code = 0, Exception $previous = null)
    {
        return new self(sprintf('Workflow "%s" not found.', $workflowName), $code, $previous);
    }

    /**
     * Create exception with the workflow name.
     *
     * @param EntityId  $entityId Entity id.
     * @param int       $code     Error code.
     * @param Exception $previous Previous thrown exception.
     *
     * @return self
     */
    public static function forEntity(EntityId $entityId, int $code = 0, Exception $previous = null)
    {
        return new self(sprintf('No workflow found for entity "%s".', $entityId), $code, $previous);
    }
}
