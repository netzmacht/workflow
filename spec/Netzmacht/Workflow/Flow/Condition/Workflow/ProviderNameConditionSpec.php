<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Flow\Workflow;
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
    protected static $entity = array('id' => 5);

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

    function it_matches_against_configurabled_provider_name(Workflow $workflow, EntityId $entityId)
    {
        $this->setProviderName('test');

        $entityId->getProviderName()->willReturn('test');

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);

        $this->setProviderName('test2');
        $this->match($workflow, $entityId, static::$entity)->shouldReturn(false);
    }

    function it_matches_against_workflow_provider_name(Workflow $workflow, EntityId $entityId)
    {
        $workflow->getProviderName()->willReturn('test');
        $entityId->getProviderName()->willReturn('test');

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }
}
