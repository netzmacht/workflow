<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ProviderTypeConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Workflow
 * @mixin ProviderNameCondition
 */
class ProviderNameConditionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition');
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Workflow\Condition');
    }

    function it_has_a_configurable_provider_name()
    {
        $this->setProviderName('test')->shouldReturn($this);
        $this->getProviderName()->shouldReturn('test');
    }

    function it_matches_against_configurabled_provider_name(Workflow $workflow, Entity $entity, EntityId $entityId)
    {
        $this->setProviderName('test');

        $entity->getEntityId()->willReturn($entityId);
        $entityId->getProviderName()->willReturn('test');

        $this->match($workflow, $entity)->shouldReturn(true);

        $this->setProviderName('test2');
        $this->match($workflow, $entity)->shouldReturn(false);
    }

    function it_matches_against_workflow_provider_name(Workflow $workflow, Entity $entity, EntityId $entityId)
    {
        $workflow->getProviderName()->willReturn('test');

        $entity->getEntityId()->willReturn($entityId);
        $entityId->getProviderName()->willReturn('test');

        $this->match($workflow, $entity)->shouldReturn(true);
    }
}
