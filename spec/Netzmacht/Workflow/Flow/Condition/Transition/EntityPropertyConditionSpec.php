<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Util\Comparison;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Flow\Condition\Transition\EntityPropertyCondition;
use Netzmacht\Workflow\Flow\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EntityPropertyConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin EntityPropertyCondition
 */
class EntityPropertyConditionSpec extends ObjectBehavior
{
    const PROPERTY_NAME = 'test';

    const VALUE = 'val';
    
    const OPERATOR = Comparison::EQUALS;

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\EntityPropertyCondition');
    }

    function it_has_a_property()
    {
        $this->setProperty(self::PROPERTY_NAME)->shouldReturn($this);
        $this->getProperty()->shouldReturn(self::PROPERTY_NAME);
    }

    function it_has_an_operator()
    {
        $this->setOperator(self::OPERATOR)->shouldReturn($this);
        $this->getOperator()->shouldReturn(self::OPERATOR);
    }

    function it_has_a_value()
    {
        $this->setValue(self::VALUE)->shouldReturn($this);
        $this->getValue()->shouldReturn(self::VALUE);
    }
    
    function it_matches_if_comparison_does(Transition $transition, Item $item, Context $context, Entity $entity)
    {
        $this->setValue(static::VALUE);
        $this->setOperator(self::OPERATOR);
        $this->setProperty(self::PROPERTY_NAME);

        $item->getEntity()->willReturn($entity);
        $entity->getProperty(static::PROPERTY_NAME)->willReturn(static::VALUE);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    function it_matches_if_comparison_does_not(Transition $transition, Item $item, Context $context, Entity $entity)
    {
        $this->setValue(static::VALUE);
        $this->setOperator(self::OPERATOR);
        $this->setProperty(self::PROPERTY_NAME);

        $item->getEntity()->willReturn($entity);
        $entity->getProperty(static::PROPERTY_NAME)->willReturn('other_value');

        $this->match($transition, $item, $context)->shouldReturn(false);
    }
}
