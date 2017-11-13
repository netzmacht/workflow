<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
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
 */
class OrConditionSpec extends ObjectBehavior
{
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Flow\Context\ErrorCollection';

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
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $conditionA->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);
        $conditionB->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_not_match_if_all_children_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $conditionA->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);
        $conditionB->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_matches_if_no_children_exists(
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }
}
