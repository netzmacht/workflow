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

namespace spec\Netzmacht\Workflow\Flow\Security;

use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

/**
 * Class PermissionSpec
 *
 * @package spec\Netzmacht\Workflow\Security
 */
class PermissionSpec extends ObjectBehavior
{
    function let(Workflow $workflow)
    {
        $workflow->getName()->willReturn('workflow');
        $this->beConstructedThrough('forWorkflow', [$workflow, 'perm']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Permission::class);
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

    function it_reconstitutes_from_string()
    {
        $this->beConstructedThrough('fromString', ['workflow:perm']);

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }

    function it_reconstitutes_for_workflow_name()
    {
        $this->beConstructedThrough('forWorkflowName', ['workflow', 'perm']);

        $this->getWorkflowName()->shouldReturn('workflow');
        $this->getPermissionId()->shouldReturn('perm');
    }
}
