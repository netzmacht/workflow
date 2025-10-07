<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;
use Override;

class PayloadPropertyCondition implements Condition
{
    /**
     * Payload property name.
     */
    private string $property;

    /**
     * Expected value.
     */
    private mixed $value;

    /**
     * Comparison operator.
     */
    private string $operator;

    /**
     * @param string $property Payload property name.
     * @param mixed  $value    Expected value.
     * @param string $operator Comparison operator.
     */
    public function __construct(string $property, mixed $value, string $operator = Comparison::EQUALS)
    {
        $this->property = $property;
        $this->value    = $value;
        $this->operator = $operator;
    }

    #[Override]
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $payloadValue = $context->getPayload()->get($this->property);

        if (Comparison::compare($payloadValue, $this->value, $this->operator)) {
            return true;
        }

        $context->addError(
            'transition.condition.payload_property.failed',
            [
                'property' => $this->property,
                'expected' => $this->value,
                'actual'   => $payloadValue,
                'operator' => $this->operator,
            ],
        );

        return false;
    }
}
