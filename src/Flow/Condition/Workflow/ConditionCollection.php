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
     * @var Condition[]|iterable
     */
    protected $conditions = array();

    /**
     * Construct.
     *
     * @param Condition[]|iterable $conditions Conditions.
     */
    public function __construct(iterable $conditions = array())
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
    public function addCondition(Condition $condition): self
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Add multiple conditions.
     *
     * @param Condition[]|iterable $conditions Array of conditions.
     *
     * @return $this
     */
    public function addConditions(iterable $conditions): self
    {
        Assertion::allIsInstanceOf($conditions, Condition::class);

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }

        return $this;
    }

    /**
     * Get all conditions.
     *
     * @return Condition[]|iterable
     */
    public function getConditions(): iterable
    {
        return $this->conditions;
    }

    /**
     * Remove condition from collection.
     *
     * @param Condition $condition Condition to remove.
     *
     * @return $this
     */
    public function removeCondition(Condition $condition): self
    {
        foreach ($this->conditions as $index => $value) {
            if ($value === $condition) {
                unset($this->conditions[$index]);
            }
        }

        return $this;
    }
}
