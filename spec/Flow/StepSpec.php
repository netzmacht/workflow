<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Security\Permission;
use PhpSpec\ObjectBehavior;

class StepSpec extends ObjectBehavior
{
    public const string NAME          = 'test';
    public const string LABEL         = 'label';
    public const string WORKFLOW_NAME = 'workflow';

    public function let(): void
    {
        $this->beConstructedWith(self::NAME, self::LABEL, [], self::WORKFLOW_NAME);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Step');
    }

    public function it_behaves_like_base_object(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Base');
    }

    public function it_is_not_final_by_default(): void
    {
        $this->isFinal()->shouldReturn(false);
    }

    public function it_can_be_final(): void
    {
        $this->setFinal(true)->shouldReturn($this);
        $this->isFinal()->shouldReturn(true);
    }

    public function it_has_no_allowed_transitions_by_default(): void
    {
        $this->getAllowedTransitions()->shouldBeEqualTo([]);
    }

    public function it_allows_transition(): void
    {
        $this->isTransitionAllowed('test')->shouldReturn(false);
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);
    }

    public function it_disallows_transition(): void
    {
        $this->allowTransition('test');
        $this->isTransitionAllowed('test')->shouldReturn(true);
        $this->disallowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }

    public function it_returns_allowed_transitions(): void
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->allowTransition('bar')->shouldReturn($this);

        $this->getAllowedTransitions()->shouldReturn(['test', 'bar']);
    }

    public function it_does_not_allow_transition_when_being_final(): void
    {
        $this->allowTransition('test')->shouldReturn($this);
        $this->isTransitionAllowed('test')->shouldReturn(true);

        $this->setFinal(true);
        $this->isTransitionAllowed('test')->shouldReturn(false);
    }

    public function it_have_a_permission(Permission $permission): void
    {
        $permission->equals($permission)->willReturn(false);

        $this->getPermission()->shouldReturn(null);
        $this->hasPermission($permission)->shouldReturn(false);

        $permission->equals($permission)->willReturn(true);

        $this->setPermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(true);
        $this->getPermission()->shouldReturn($permission);
    }

    public function it_might_know_the_workflow_name(): void
    {
        $this->getWorkflowName()->shouldBe(self::WORKFLOW_NAME);
    }

    public function it_migt_have_not_the_workflow_name(): void
    {
        $this->beConstructedWith(self::NAME, self::LABEL);

        $this->getWorkflowName()->shouldBeNull();
    }
}
