<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection;
use Netzmacht\Workflow\Flow\Condition\Transition\OrCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class OrConditionSpec extends ObjectBehavior
{
    public const string ERROR_COLLECTION_CLASS = ErrorCollection::class;

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OrCondition::class);
    }

    public function it_is_a_condition_collection(): void
    {
        $this->shouldHaveType(ConditionCollection::class);
    }

    public function it_matches_if_any_child_matches(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $errorCollection = new ErrorCollection();
        $context->createCleanCopy()->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $conditionA->match($transition, $item, $context)->willReturn(false);
        $conditionB->match($transition, $item, $context)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    public function it_does_not_match_if_all_children_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $errorCollection = new ErrorCollection();

        $context->createCleanCopy()->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $conditionA->match($transition, $item, $context)->willReturn(false);
        $conditionB->match($transition, $item, $context)->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context)->shouldReturn(false);
    }

    public function it_matches_if_no_children_exists(
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $this->match($transition, $item, $context)->shouldReturn(true);
    }
}
