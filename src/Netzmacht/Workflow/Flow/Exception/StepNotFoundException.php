<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Exception;

use Exception;

/**
 * Class StepNotFoundException is thrown when step is not found.
 *
 * @package Netzmacht\Workflow\Flow\Exception
 */
class StepNotFoundException extends WorkflowException
{
    /**
     * Construct.
     *
     * @param string    $stepName     The step name which is not found.
     * @param string    $workflowName Current workflow name.
     * @param int       $code         Error code.
     * @param Exception $previous     Previous thrown exception.
     */
    public function __construct($stepName, $workflowName, $code = 0, Exception $previous = null)
    {
        parent::__construct(
            sprintf('Step "%s" is not part of workflow "%s"', $stepName, $workflowName),
            $code,
            $previous
        );
    }
}
