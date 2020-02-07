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

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Security\Permission;
use PhpSpec\ObjectBehavior;

/**
 * Class StepSpec
 *
 * @package spec\Netzmacht\Workflow\Flow
 */
class StepSpec extends ObjectBehavior
{
    const NAME = 'test';
    const LABEL = 'label';
    const WORKFLOW_NAME = 'workflow';

    function let()
    {
        $this->beConstructedWith(self::NAME, self::LABEL, [], self::WORKFLOW_NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Step');
    }

    function it_behaves_like_base_object()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Base');
    }

    function it_is_not_final_by_default()
    {
        $this->isFinal()->shouldReturn(false);
    }

    function it_can_be_final()
    {
        $this->setFinal(true)->shouldReturn($this);
        $this->isFinal()->shouldReturn(true);
    }

    function it_has_no_allowed_transitions_by_default()
    {
        $this->getAllowedTransitions()->shouldBeEqualTo([]);
    }

    function it_allows_transition()
    {
        $this->isTransitionAllowed('test')->shouldReturn(false);
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);
    }

    function it_disallows_transition()
    {
        $this->allowTransition('test');
        $this->isTransitionAllowed('test')->shouldReturn(true);
        $this->disallowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }

    function it_returns_allowed_transitions()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->allowTransition('bar')->shouldReturn($this);

        $this->getAllowedTransitions()->shouldReturn(['test', 'bar']);
    }

    function it_does_not_allow_transition_when_being_final()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);

        $this->setFinal(true);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }

    function it_have_a_permission(Permission $permission)
    {
        $permission->equals($permission)->willReturn(false);

        $this->getPermission()->shouldReturn(null);
        $this->hasPermission($permission)->shouldReturn(false);

        $permission->equals($permission)->willReturn(true);

        $this->setPermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(true);
        $this->getPermission()->shouldReturn($permission);
    }

    function it_might_know_the_workflow_name(): void
    {
        $this->getWorkflowName()->shouldBe(self::WORKFLOW_NAME);
    }

    function it_migt_have_not_the_workflow_name(): void
    {
        $this->beConstructedWith(self::NAME, self::LABEL);

        $this->getWorkflowName()->shouldBeNull();
    }
}
