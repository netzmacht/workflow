<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Condition\Workflow\ConditionCollection as AbstractConditionCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ConditionCollectionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Workflow
 * @mixin ConditionCollection
 */
class ConditionCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Netzmacht\Workflow\Flow\Condition\Workflow\ConditionCollection');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ConditionCollection');
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
    public function match(Workflow $workflow, EntityId $entityId, Entity $entity)
    {
    }
}
