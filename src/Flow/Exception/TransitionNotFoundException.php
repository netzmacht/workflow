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

namespace Netzmacht\Workflow\Flow\Exception;

use Exception;

/**
 * Class TransitionNotFoundException is thrown then transition was not found.
 *
 * @package Netzmacht\Workflow\Flow\Exception
 */
class TransitionNotFoundException extends \Exception
{
    /**
     * Construct.
     *
     * @param string    $transitionName The not found transition name.
     * @param string    $workflowName   Current workflow name.
     * @param int       $code           Error code.
     * @param Exception $previous       Previous thrown exception.
     */
    public function __construct($transitionName, $workflowName, $code = 0, Exception $previous = null)
    {
        parent::__construct(
            sprintf('Transition "%s" not found in workflow "%s"', $transitionName, $workflowName),
            $code,
            $previous
        );
    }
}
