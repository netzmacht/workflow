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

use Assert\Assertion;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class ConditionCollection contains child conditions which are called during match.
 *
 * @package Netzmacht\Workflow\Flow\Transition\Condition
 */
abstract class ConditionCollection extends AbstractCondition
{
    /**
     * All child conditions of the collection.
     *
     * @var Condition[]
     */
    protected $conditions = array();

    /**
     * All child errors.
     *
     * @var array
     */
    protected $childErrors = array();

    /**
     * Add condition.
     *
     * @param Condition $condition Condition being added.
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Remove condition from collection.
     *
     * @param Condition $condition Condition being removed.
     *
     * @return $this
     */
    public function removeCondition(Condition $condition)
    {
        foreach ($this->conditions as $index => $value) {
            if ($value === $condition) {
                unset($this->conditions[$index]);
            }
        }

        return $this;
    }

    /**
     * Get child conditions.
     *
     * @return Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Add multiple conditions.
     *
     * @param array $conditions Array of conditions being added.
     *
     * @return $this
     *
     * @throws \Assert\InvalidArgumentException If array contains an invalid condition.
     */
    public function addConditions(array $conditions)
    {
        Assertion::allIsInstanceOf($conditions, 'Netzmacht\Workflow\Flow\Condition\Transition\Condition');

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }

        return $this;
    }

    /**
     * Add error from a child condition.
     *
     * @param Condition $condition Child condition.
     *
     * @return $this
     */
    protected function addError(Condition $condition)
    {
        $this->childErrors[] = $condition->getError();

        return $this;
    }

    /**
     * Check if errors exists.
     *
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->childErrors);
    }

    /**
     * Reset error state.
     *
     * @return true
     */
    protected function pass()
    {
        $this->childErrors = array();

        return parent::pass();
    }

    /**
     * Add error message.
     *
     * @param string $error Message to be added.
     *
     * @return false
     */
    protected function fail($error)
    {
        return parent::fail($error, array($this->childErrors));
    }
}
