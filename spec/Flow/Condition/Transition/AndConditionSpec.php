<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

use function expect;

final class AndConditionSpec extends ObjectBehavior
{
    public const string ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Flow\Context\ErrorCollection';

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    public function it_is_a_condition_collection(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }

    public function it_matches_if_all_children_matches(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
    ): void {
        $context = new Context();

        $conditionA->match($transition, $item, $context)->willReturn(true);
        $conditionB->match($transition, $item, $context)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    public function it_does_not_match_if_one_child_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Transition $transition,
        Item $item,
    ): void {
        $context = new Context();

        $conditionA->match($transition, $item, $context)->willReturn(true);
        $conditionB->match($transition, $item, $context)->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($transition, $item, $context)->shouldReturn(false);

        expect($context->getErrorCollection()->hasErrors())->shouldBe(true);
    }

    public function it_matches_if_no_children_exists(
        Transition $transition,
        Item $item,
    ): void {
        $this->match($transition, $item, new Context())->shouldReturn(true);
    }
}
