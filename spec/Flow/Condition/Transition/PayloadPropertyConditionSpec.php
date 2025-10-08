<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\PayloadPropertyCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\Properties;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class PayloadPropertyConditionSpec extends ObjectBehavior
{
    public function let(Context $context): void
    {
        $payload = new Properties(['foo' => 'bar']);
        $context->getPayload()->willReturn($payload);
        $context->addError(Argument::cetera())->willReturn($context);

        $this->beConstructedWith('foo', 'bar');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PayloadPropertyCondition::class);
    }

    public function it_is_a_transition_condition(): void
    {
        $this->shouldImplement(Condition::class);
    }

    public function it_compares_payload_property_with_expected_value(
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $this->match($transition, $item, $context);
    }

    public function it_supports_different_operators(
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $this->beConstructedWith('foo', 3, Comparison::LESSER_THAN);
        $this->match($transition, $item, $context);
    }

    public function it_creates_an_error_when_comparison_fails(
        Transition $transition,
        Item $item,
        Context $context,
    ): void {
        $this->beConstructedWith('foo', 3, Comparison::EQUALS);
        $context->addError('transition.condition.payload_property.failed', Argument::type('array'))
            ->shouldBeCalled();

        $this->match($transition, $item, $context);
    }
}
