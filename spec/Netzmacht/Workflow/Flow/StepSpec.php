<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Flow\Step;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StepSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin Step
 */
class StepSpec extends ObjectBehavior
{
    const NAME = 'test';
    const LABEL = 'label';

    function let()
    {
        $this->beConstructedWith(self::NAME, self::LABEL);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Step');
    }

    function it_behaves_like_base_object()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Base');
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
        $this->getAllowedTransitions()->shouldBeEqualTo(array());
    }

    function it_allows_transition()
    {
        $this->isTransitionAllowed('test')->shouldReturn(false);
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);
    }

    function it_returns_allowed_transitions()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->allowTransition('bar')->shouldReturn($this);

        $this->getAllowedTransitions()->shouldReturn(array('test', 'bar'));
    }

    function it_does_not_allow_transition_when_being_final()
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);

        $this->setFinal(true);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }

    function it_can_be_assigned_to_a_role(Role $role)
    {
        $role->equals($role)->willReturn(true);

        $this->isAssignedTo($role)->shouldReturn(false);
        $this->getRole()->shouldReturn(null);

        $this->assignTo($role)->shouldReturn($this);
        $this->isAssignedTo($role)->shouldReturn(true);
        $this->getRole()->shouldReturn($role);
    }
}
