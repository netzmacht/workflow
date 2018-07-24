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
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

/**
 * Class AndConditionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow\Condition\Workflow
 */
class AndConditionSpec extends ObjectBehavior
{
    protected static $entity = ['id' => 5];

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition');
    }

    function it_is_a_condition_collection()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\ConditionCollection');
    }

    function it_matches_if_all_children_matches(
        Condition $conditionA,
        Condition $conditionB,
        Workflow $workflow
    ) {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $conditionA->match($workflow, $entityId, static::$entity)->willReturn(true);
        $conditionB->match($workflow, $entityId, static::$entity)->willReturn(true);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }

    function it_does_not_match_if_one_child_does_not(
        Condition $conditionA,
        Condition $conditionB,
        Workflow $workflow
    ) {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $conditionA->match($workflow, $entityId, static::$entity)->willReturn(true);
        $conditionB->match($workflow, $entityId, static::$entity)->willReturn(false);

        $this->addCondition($conditionA);
        $this->addCondition($conditionB);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(false);
    }

    function it_matches_if_no_children_exists(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId('test', 5);

        $this->match($workflow, $entityId, static::$entity)->shouldReturn(true);
    }
}
