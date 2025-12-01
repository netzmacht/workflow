<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\PayloadPropertyCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Util\Comparison;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use function expect;

final class PayloadPropertyConditionSpec extends ObjectBehavior
{
    private Context $context;

    public function let(Workflow $workflow): void
    {
        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);

        $this->context = new Context();
        $this->context->getPayload()->set('foo', 'bar');

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

    public function it_compares_payload_property_with_expected_value(Workflow $workflow): void
    {
        $item = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $this->match(new Transition('transition', $workflow->getWrappedObject()), $item, $this->context);
    }

    public function it_supports_different_operators(Workflow $workflow): void
    {
        $item = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $this->beConstructedWith('foo', 3, Comparison::LESSER_THAN);
        $this->match(new Transition('transition', $workflow->getWrappedObject()), $item, $this->context);
    }

    public function it_creates_an_error_when_comparison_fails(Workflow $workflow): void
    {
        $this->beConstructedWith('foo', 3, Comparison::EQUALS);

        $item       = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);
        $transition = new Transition('transition', $workflow->getWrappedObject());
        $this->match($transition, $item, $this->context)->shouldReturn(false);

        expect($this->context->getErrorCollection()->getErrors())->shouldBe(
            [
                [
                    'transition.condition.payload_property.failed',
                    [
                        'property' => 'foo',
                        'expected' => 3,
                        'actual'   => 'bar',
                        'operator' => Comparison::EQUALS,
                    ],
                    null,
                ],
            ],
        );
    }
}
