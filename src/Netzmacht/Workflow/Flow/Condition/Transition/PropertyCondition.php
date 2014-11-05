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
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Class PropertyCondition compares an entity property against a defined value.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class PropertyCondition implements Condition
{
    /**
     * Name of the property.
     *
     * @var string
     */
    private $property;

    /**
     * Comparison operator.
     *
     * @var string
     */
    private $operator = Comparison::EQUALS;

    /**
     * Value to compare with.
     *
     * @var mixed
     */
    private $value;

    /**
     * Get comparison operator.
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set property name.
     *
     * @param string $operator Comparison operator name.
     *
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get Property name.
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set comparison property.
     *
     * @param string $property Comparison property.
     *
     * @return $this
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get value to compare agianst.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value to compare against.
     *
     * @param mixed $value The comparison value.
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Consider if condition matches for the given entity.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        $value = $this->getEntityValue($item);

        if (Comparison::compare($value, $this->value, $this->operator)) {
            return true;
        }

        return false;
    }

    /**
     * Describes an failed condition.
     *
     * It returns an array with 2 parameters. First one is the error message code. The second one are the params to
     * be replaced in the message.
     *
     * Example return array('transition.condition.example', array('name', 'value'));
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return array
     */
    public function describeError(Transition $transition, Item $item, Context $context)
    {
        $value = $this->getEntityValue($item);

        return array('transition.condition.property', array($value, $this->value, $this->operator));
    }

    /**
     * Get value from the entity.
     *
     * @param Item $item Current workflow item.
     *
     * @return mixed
     */
    protected function getEntityValue(Item $item)
    {
        $entity = $item->getEntity();

        // no access to id by the entity
        if ($this->property == 'id') {
            return $entity->getEntityId()->getIdentifier();
        }

        return $entity->getProperty($this->property);
    }
}
