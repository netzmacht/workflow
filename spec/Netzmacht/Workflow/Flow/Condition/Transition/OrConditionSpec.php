<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\OrCondition;
use Netzmacht\Workflow\Flow\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class OrConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin OrCondition
 */
class OrConditionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\OrCondition');
    }

    function it_is_a_condition_collection()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }

    function it_matches_if_any_child_matches(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context
    ) {
        $conditionA->match($transition, $item, $context)->willReturn(false);
        $conditionB->match($transition, $item, $context)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    function it_does_not_match_if_all_children_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context
    ) {
        $conditionA->match($transition, $item, $context)->willReturn(false);
        $conditionB->match($transition, $item, $context)->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(false);
    }

    function it_matches_if_no_children_exists(Transition $transition, Item $item, Context $context)
    {
        $this->match($transition, $item, $context)->shouldReturn(true);
    }
}
