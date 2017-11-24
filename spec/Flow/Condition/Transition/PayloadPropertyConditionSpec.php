<?php

/**
 * workflow.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

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

/**
 * Class PayloadPropertyConditionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 */
class PayloadPropertyConditionSpec extends ObjectBehavior
{
    function let(Context $context, Properties $payload)
    {
        $context->getPayload()->willReturn($payload);

        $this->beConstructedWith('foo', 'bar');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PayloadPropertyCondition::class);
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement(Condition::class);
    }

    function it_compares_payload_property_with_expected_value(
        Transition $transition,
        Item $item,
        Context $context,
        Properties $payload
    ) {
        $payload->get('foo')->willReturn('bar');

        $this->match($transition, $item, $context);
    }

    function it_supports_different_operators(
        Transition $transition,
        Item $item,
        Context $context,
        Properties $payload
    ) {
        $this->beConstructedWith('foo', 3, Comparison::LESSER_THAN);
        $payload->get('foo')->willReturn(2);
        $this->match($transition, $item, $context);
    }

    function it_creates_an_error_when_comparison_fails(
        Transition $transition,
        Item $item,
        Context $context,
        Properties $payload
    ) {
        $payload->get('foo')->willReturn('baz');

        $context->addError('transition.condition.payload_property.failed', Argument::type('array'))
            ->shouldBeCalled();

        $this->match($transition, $item, $context);
    }
}
