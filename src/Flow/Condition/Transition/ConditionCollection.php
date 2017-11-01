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

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Assert\Assertion;

/**
 * Class ConditionCollection contains child conditions which are called during match.
 *
 * @package Netzmacht\Workflow\Flow\Transition\Condition
 */
abstract class ConditionCollection implements Condition
{
    /**
     * All child conditions of the collection.
     *
     * @var Condition[]
     */
    protected $conditions = array();

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
}
