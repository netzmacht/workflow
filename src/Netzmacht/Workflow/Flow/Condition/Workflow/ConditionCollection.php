<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Assert\Assertion;

/**
 * Class ConditionCollection contains child condition which are called during match.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Workflow
 */
abstract class ConditionCollection implements Condition
{
    /**
     * Child conditions of the collection.
     *
     * @var Condition[]|array
     */
    protected $conditions = array();

    /**
     * Construct.
     *
     * @param Condition[]|array $conditions Conditions.
     */
    public function __construct(array $conditions = array())
    {
        $this->addConditions($conditions);
    }

    /**
     * Add new child condition.
     *
     * @param Condition $condition Child condition.
     *
     * @return $this
     */
    public function addCondition(Condition $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Add multiple conditions.
     *
     * @param array $conditions Array of conditions.
     *
     * @return $this
     */
    public function addConditions(array $conditions)
    {
        Assertion::allIsInstanceOf($conditions, 'Netzmacht\Workflow\Flow\Condition\Workflow\Condition');

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }

        return $this;
    }

    /**
     * Get all conditions.
     *
     * @return array|Condition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
