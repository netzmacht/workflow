<?php

namespace spec\Netzmacht\Workflow\Security;

use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Security\Permission;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PermissionSpec
 * @package spec\Netzmacht\Workflow\Security
 * @mixin Permission
 */
class PermissionSpec extends ObjectBehavior
{
    function let(Workflow $workflow)
    {
        $workflow->getName()->willReturn('workflow');
        $this->beConstructedThrough('forWorkflow', array($workflow, 'perm'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Security\Permission');
    }

    function it_has_a_workflow_name()
    {
        $this->getWorkflowName()->shouldReturn('workflow');
    }

    function it_has_a_permission_id()
    {
        $this->getPermissionId()->shouldReturn('perm');
    }

    function it_equals_if_workflow_and_permission_id_matches(Permission $permission)
    {
        $permission->__toString()->willReturn('workflow:perm');

        $this->equals($permission)->shouldReturn(true);
    }

    function it_does_not_equals_if_not_the_same_workflow(Permission $permission)
    {
        $permission->__toString()->willReturn('workflow2:perm');

        $this->equals($permission)->shouldReturn(false);
    }

    function it_casts_to_string()
    {
        $this->__toString()->shouldReturn('workflow:perm');
    }

    function it_reconstitutes_form_string()
    {
        $this->beConstructedThrough('fromString', array('workflow:perm'));

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }
    
    function it_reconstitutes_for_workflow_name()
    {
        $this->beConstructedThrough('forWorkflowName', array('workflow', 'perm'));

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }
}
