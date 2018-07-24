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

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class AndConditionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 */
class AndConditionSpec extends ObjectBehavior
{
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Flow\Context\ErrorCollection';

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
        $context->createCleanCopy()->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $conditionA->match($transition, $item, $context)->willReturn(true);
        $conditionB->match($transition, $item, $context)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    function it_does_not_match_if_one_child_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $context->createCleanCopy()->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $conditionA->match($transition, $item, $context)->willReturn(true);
        $conditionB->match($transition, $item, $context)->willReturn(false);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(false);
    }

    function it_matches_if_no_children_exists(
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $context->createCleanCopy()->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }
}
