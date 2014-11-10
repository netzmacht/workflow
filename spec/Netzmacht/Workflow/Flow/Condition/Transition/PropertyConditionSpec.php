<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Condition\Transition\PropertyCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PropertyConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin PropertyCondition
 */
class PropertyConditionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\PropertyCondition');
    }

    function it_has_a_property_name()
    {
        $this->setProperty('test')->shouldReturn($this);
        $this->getProperty()->shouldReturn('test');
    }

    function it_compares_with_equals_by_default()
    {
        $this->getOperator()->shouldReturn(Comparison::EQUALS);
    }

    function it_has_an_operator()
    {
        $this->setOperator(Comparison::GREATER_THAN)->shouldReturn($this);
        $this->getOperator()->shouldReturn(Comparison::GREATER_THAN);
    }

    function it_has_a_value()
    {
        $this->setValue(10)->shouldReturn($this);
        $this->getValue()->shouldReturn(10);
    }

    function it_matches_if_comparison_is_true(
        Transition $transition,
        Item $item,
        Entity $entity,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->setOperator(Comparison::GREATER_THAN);
        $this->setProperty('test');
        $this->setValue(5);

        $item->getEntity()->willReturn($entity);
        $entity->getProperty('test')->willReturn(10);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_not_match_if_comparison_does(
        Transition $transition,
        Item $item,
        Entity $entity,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->setOperator(Comparison::LESSER_THAN);
        $this->setProperty('test');
        $this->setValue(5);

        $item->getEntity()->willReturn($entity);
        $entity->getProperty('test')->willReturn(10);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }
}
