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
use Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection as AbstractConditionCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

/**
 * Class ConditionCollectionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 */
class ConditionCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }


    function it_adds_condition(Condition $condition)
    {
        $this->addCondition($condition)->shouldReturn($this);
        $this->getConditions()->shouldReturn([$condition]);
    }

    function it_adds_conditions(Condition $condition)
    {
        $this->addConditions([$condition])->shouldReturn($this);
        $this->getConditions()->shouldReturn([$condition]);
    }

    function it_removes_a_condition(Condition $condition)
    {
        $this->addCondition($condition);
        $this->getConditions()->shouldReturn([$condition]);
        $this->removeCondition($condition)->shouldReturn($this);
        $this->getConditions()->shouldReturn([]);
    }

    function it_throws_if_invalid_condition_passed()
    {
        $this->shouldThrow('Assert\InvalidArgumentException')->duringAddConditions(['test']);
    }
}

class ConditionCollection extends AbstractConditionCollection
{
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        return false;
    }
}
