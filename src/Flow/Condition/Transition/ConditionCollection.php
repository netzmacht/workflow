<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Assert\Assertion;
use Assert\InvalidArgumentException;

/**
 * Class ConditionCollection contains child conditions which are called during match.
 */
abstract class ConditionCollection implements Condition
{
    /**
     * All child conditions of the collection.
     *
     * @var Condition[]
     */
    protected array $conditions = [];

    /** @param Condition[]|iterable $conditions List of child conditions. */
    public function __construct(iterable $conditions = [])
    {
        $this->addConditions($conditions);
    }

    /**
     * Add condition.
     *
     * @param Condition $condition Condition being added.
     *
     * @return $this
     */
    public function addCondition(Condition $condition): self
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
    public function removeCondition(Condition $condition): self
    {
        foreach ($this->conditions as $index => $value) {
            if ($value !== $condition) {
                continue;
            }

            unset($this->conditions[$index]);
        }

        return $this;
    }

    /**
     * Get child conditions.
     *
     * @return Condition[]|iterable
     */
    public function getConditions(): iterable
    {
        return $this->conditions;
    }

    /**
     * Add multiple conditions.
     *
     * @param Condition[]|iterable $conditions Array of conditions being added.
     *
     * @return $this
     *
     * @throws InvalidArgumentException If array contains an invalid condition.
     */
    public function addConditions(iterable $conditions): self
    {
        Assertion::allIsInstanceOf($conditions, Condition::class);

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }

        return $this;
    }
}
