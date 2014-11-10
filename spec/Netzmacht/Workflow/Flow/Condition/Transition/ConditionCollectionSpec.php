<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection as AbstractConditionCollection;
use Netzmacht\Workflow\Flow\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ConditionCollectionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin ConditionCollection
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
        $this->getConditions()->shouldReturn(array($condition));
    }

    function it_adds_conditions(Condition $condition)
    {
        $this->addConditions(array($condition))->shouldReturn($this);
        $this->getConditions()->shouldReturn(array($condition));
    }

    function it_removes_a_condition(Condition $condition)
    {
        $this->addCondition($condition);
        $this->getConditions()->shouldReturn(array($condition));
        $this->removeCondition($condition)->shouldReturn($this);
        $this->getConditions()->shouldReturn(array());
    }

    function it_throws_if_invalid_condition_passed()
    {
        $this->shouldThrow('Assert\InvalidArgumentException')->duringAddConditions(array('test'));
    }
}

class ConditionCollection extends AbstractConditionCollection
{
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
    }
}
