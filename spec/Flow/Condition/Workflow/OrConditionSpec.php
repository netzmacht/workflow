<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

class OrConditionSpec extends ObjectBehavior
{
    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 4];

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\OrCondition');
    }

    public function it_is_a_condition_collection(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ConditionCollection');
    }

    public function it_matches_if_one_child_matches(
        Condition $conditionA,
        Condition $conditionB,
        Workflow $workflow,
    ): void {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $conditionA->match($workflow, $entityId, static::$entity)->willReturn(false);
        $conditionB->match($workflow, $entityId, static::$entity)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }

    public function it_does_not_match_if_no_child_matches(
        Condition $conditionA,
        Condition $conditionB,
        Workflow $workflow,
    ): void {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $conditionA->match($workflow, $entityId, static::$entity)->willReturn(false);
        $conditionB->match($workflow, $entityId, static::$entity)->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(false);
    }

    public function it_matches_if_no_children_exists(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }
}
