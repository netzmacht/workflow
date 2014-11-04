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

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Class EntityPropertyCondition allows to define condition based on the entity properties.
 *
 * @package Netzmacht\Workflow\Flow\Transition\Condition
 */
class EntityPropertyCondition implements Condition
{
    /**
     * The property name.
     *
     * @var string
     */
    private $property;

    /**
     * The comparison operator.
     *
     * @var string
     */
    private $operator;

    /**
     * The value to compare with.
     *
     * @var mixed
     */
    private $value;

    /**
     * Get the operator.
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set the operator.
     *
     * @param string $operator Comparison operator.
     *
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get the property.
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set property name which shall be compared.
     *
     * @param string $property Property name.
     *
     * @return $this
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get the comparison value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value to which the entity property should be compared.
     *
     * @param mixed $value The value.
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Consider if property condition matches.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        return Comparison::compare(
            $item->getEntity()->getProperty($this->property),
            $this->value,
            $this->operator
        );
    }
}
