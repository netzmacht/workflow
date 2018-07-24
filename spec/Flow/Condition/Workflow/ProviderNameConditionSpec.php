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

namespace spec\Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

/**
 * Class ProviderTypeConditionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow\Condition\Workflow
 */
class ProviderNameConditionSpec extends ObjectBehavior
{
    protected static $entity = ['id' => 5];

    function let()
    {
        $this->beConstructedWith('test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition');
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Workflow\Condition');
    }

    function it_has_a_configurable_provider_name()
    {
        $this->getProviderName()->shouldReturn('test');
    }

    function it_matches_against_configurabled_provider_name(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);
        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);

        $entityId = EntityId::fromProviderNameAndId('test2', 5);
        $this->match($workflow, $entityId, static::$entity)->shouldReturn(false);
    }

    function it_matches_against_workflow_provider_name(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);
        $workflow->getProviderName()->willReturn('test');

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }
}
