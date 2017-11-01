<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Condition\Transition\AndCondition;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class AndConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 */
class AndConditionSpec extends ObjectBehavior
{
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    function it_is_a_condition_collection()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }

    function it_matches_if_all_children_matches(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $conditionA->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);
        $conditionB->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_not_match_if_one_child_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $conditionA->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);
        $conditionB->match($transition, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_matches_if_no_children_exists(
        Transition $transition, 
        Item $item, 
        Context $context,
        ErrorCollection $errorCollection)
    {
        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }
}
