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

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Util\Comparison;

/**
 * Class PayloadPropertyCondition
 */
class PayloadPropertyCondition implements Condition
{
    /**
     * Payload property name.
     *
     * @var string
     */
    private $property;

    /**
     * Expected value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Comparison operator.
     *
     * @var string
     */
    private $operator;

    /**
     * PayloadPropertyCondition constructor.
     *
     * @param string $property Payload property name.
     * @param mixed  $value    Expected value.
     * @param string $operator Comparison operator.
     */
    public function __construct(string $property, $value, string $operator = Comparison::EQUALS)
    {
        $this->property = $property;
        $this->value    = $value;
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
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
            ]
        );

        return false;
    }
}
