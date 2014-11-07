<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Transition;

/**
 * Class AbstractCondition provides error handling for transitions.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
abstract class AbstractCondition implements Condition
{
    /**
     * Last error message.
     *
     * @var string
     */
    protected $error;

    /**
     * Error params of last error.
     *
     * @var array
     */
    protected $errorParams = array();

    /**
     * Reset error data when condition is passed. Always returns true.
     *
     * @return bool
     */
    protected function pass()
    {
        $this->errorParams = array();
        $this->error       = null;

        return true;
    }

    /**
     * Set error on failing. Always returns false.
     *
     * @param string $error  Error message code.
     * @param array  $params Error params.
     *
     * @return bool
     */
    protected function fail($error, array $params = array())
    {
        $this->error       = $error;
        $this->errorParams = $params;

        return false;
    }

    /**
     * Describes get latest error.
     *
     * @return array|bool
     */
    public function getError()
    {
        if ($this->error) {
            return array($this->error, $this->errorParams);
        }

        return false;
    }

    /**
     * Consider if an error exists error.
     *
     * @return bool
     */
    public function hasError()
    {
        if ($this->error) {
            return true;
        }

        return false;
    }
}
