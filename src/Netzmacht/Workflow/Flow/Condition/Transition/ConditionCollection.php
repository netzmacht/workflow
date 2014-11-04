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

/**
 * Class ConditionCollection contains child conditions which are called during match.
 *
 * @package Netzmacht\Workflow\Flow\Transition\Condition
 */
abstract class ConditionCollection implements Condition
{
    /**
     * @var array|Condition[]
     */
    protected $conditions = array();

    /**
     * Add condition.
     *
     * @param Condition $condition
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
     * @param Condition $condition
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
     * Get child conditions
     *
     * @return array|Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Add multiple conditions.
     *
     * @param array $conditions
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
