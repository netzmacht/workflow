<?php

namespace spec\Netzmacht\Workflow;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\StateRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ManagerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin \Netzmacht\Workflow\Manager
 */
class ManagerSpec extends ObjectBehavior
{
    const ENTITY_PROVIDER_NAME = 'provider_name';

    const ENTITY_ID = 5;

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Manager');
    }

    function let(
        TransitionHandlerFactory $transitionHandlerFactory,
        StateRepository $stateRepository,
        Workflow $workflow,
        Entity $entity,
        EntityId $entityId
    ) {
        $this->beConstructedWith($transitionHandlerFactory, $stateRepository, array($workflow));

        $entity->getEntityId()->willReturn($entityId);
    }

    function it_gets_workflow(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(true);

        $this->getWorkflow($entity)->shouldReturn($workflow);
    }

    function it_adds_workflow(Workflow $anotherWorkflow)
    {
        $anotherWorkflow->getName()->willReturn('another');

        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflowByName('another')->shouldReturn($anotherWorkflow);
    }

    function it_returns_false_if_no_workflow_found(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(false);

        $this->getWorkflow($entity)->shouldReturn(false);
    }

    function it_knows_if_matching_workflow_exists(Workflow $workflow, Entity $entity)
    {
        $workflow->match($entity)->willReturn(true);
        $this->hasWorkflow($entity)->shouldReturn(true);
    }

    function it_adds_an_workflow(Workflow $anotherWorkflow)
    {
        $this->getWorkflows()->shouldNotContain($anotherWorkflow);
        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflows()->shouldContain($anotherWorkflow);
    }

    function it_returns_false_if_no_matching_workflow_found(
        Workflow $workflow,
        Item $item,
        Entity $entity
    ) {
        $item->getEntity()->willReturn($entity);

        $workflow->match($entity)->willReturn(false);
        $this->handle($item)->shouldReturn(false);
    }

    function it_creates_an_item(
        Entity $entity,
        EntityId $entityId,
        StateRepository $stateRepository,
        TransitionHandlerFactory $transitionHandlerFactory,
        State $state
    ) {
        $stateRepository->find($entityId)->willReturn(array($state));

        $this->createItem($entity)->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }


}
