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
        parent::__construct(sprintf('Transition "%s" not found in workflow "%s"', $transitionName, $workflowName));
    }
}
