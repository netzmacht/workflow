<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use PhpSpec\ObjectBehavior;

class ConditionCollectionSpec extends ObjectBehavior
{
    public function let(): void
    {
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\ConditionCollection');
    }

    public function it_adds_condition(Condition $condition): void
    {
        $this->addCondition($condition)->shouldReturn($this);
        $this->getConditions()->shouldReturn([$condition]);
    }

    public function it_adds_conditions(Condition $condition): void
    {
        $this->addConditions([$condition])->shouldReturn($this);
        $this->getConditions()->shouldReturn([$condition]);
    }

    public function it_removes_a_condition(Condition $condition): void
    {
        $this->addCondition($condition);
        $this->getConditions()->shouldReturn([$condition]);
        $this->removeCondition($condition)->shouldReturn($this);
        $this->getConditions()->shouldReturn([]);
    }

    public function it_throws_if_invalid_condition_passed(): void
    {
        $this->shouldThrow('Assert\InvalidArgumentException')->duringAddConditions(['test']);
    }
}
