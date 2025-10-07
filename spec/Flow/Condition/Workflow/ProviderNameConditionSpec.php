<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

class ProviderNameConditionSpec extends ObjectBehavior
{
    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    public function let(): void
    {
        $this->beConstructedWith('test');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition');
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Workflow\Condition');
    }

    public function it_has_a_configurable_provider_name(): void
    {
        $this->getProviderName()->shouldReturn('test');
    }

    public function it_matches_against_configurabled_provider_name(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);
        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);

        $entityId = EntityId::fromProviderNameAndId('test2', 5);
        $this->match($workflow, $entityId, static::$entity)->shouldReturn(false);
    }

    public function it_matches_against_workflow_provider_name(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);
        $workflow->getProviderName()->willReturn('test');

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }
}
