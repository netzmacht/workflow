<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Security;

use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

final class PermissionSpec extends ObjectBehavior
{
    public function let(Workflow $workflow): void
    {
        $workflow->getName()->willReturn('workflow');
        $this->beConstructedThrough('forWorkflow', [$workflow, 'perm']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Permission::class);
    }

    public function it_has_a_workflow_name(): void
    {
        $this->getWorkflowName()->shouldReturn('workflow');
    }

    public function it_has_a_permission_id(): void
    {
        $this->getPermissionId()->shouldReturn('perm');
    }

    public function it_equals_if_workflow_and_permission_id_matches(): void
    {
        $permission = Permission::forWorkflowName('workflow', 'perm');

        $this->equals($permission)->shouldReturn(true);
    }

    public function it_does_not_equals_if_not_the_same_workflow(): void
    {
        $permission = Permission::forWorkflowName('workflow2', 'perm');

        $this->equals($permission)->shouldReturn(false);
    }

    public function it_casts_to_string(): void
    {
        $this->__toString()->shouldReturn('workflow:perm');
    }

    public function it_reconstitutes_from_string(): void
    {
        $this->beConstructedThrough('fromString', ['workflow:perm']);

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }

    public function it_reconstitutes_for_workflow_name(): void
    {
        $this->beConstructedThrough('forWorkflowName', ['workflow', 'perm']);

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }
}
