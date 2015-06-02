<?php

namespace spec\Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Handler\TransitionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ManagerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin \Netzmacht\Workflow\Manager
 */
class WorkflowManagerSpec extends ObjectBehavior
{
    const ENTITY_PROVIDER_NAME = 'provider_name';

    const ENTITY_ID = 5;

    protected static $entity = array('id' => 5);

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Manager\Manager');
    }

    function let(
        TransitionHandlerFactory $transitionHandlerFactory,
        StateRepository $stateRepository,
        Workflow $workflow
    ) {
        $this->beConstructedWith($transitionHandlerFactory, $stateRepository, array($workflow));
    }

    function it_gets_workflow(Workflow $workflow, EntityId $entityId)
    {
        $workflow->match($entityId, static::$entity)->willReturn(true);

        $this->getWorkflow($entityId, static::$entity)->shouldReturn($workflow);
    }

    function it_gets_workflow_by_item(Workflow $workflow, Item $item, EntityId $entityId)
    {
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);

        $workflow->match($entityId, static::$entity)->willReturn(true);

        $this->getWorkflowByItem($item)->shouldReturn($workflow);
    }

    function it_adds_workflow(Workflow $anotherWorkflow)
    {
        $anotherWorkflow->getName()->willReturn('another');

        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflowByName('another')->shouldReturn($anotherWorkflow);
    }

    function it_returns_false_if_no_workflow_found(Workflow $workflow, EntityId $entityId)
    {
        $workflow->match($entityId, static::$entity)->willReturn(false);

        $this->getWorkflow($entityId, static::$entity)->shouldReturn(false);
    }

    function it_knows_if_matching_workflow_exists(Workflow $workflow, EntityId $entityId)
    {
        $workflow->match($entityId, static::$entity)->willReturn(true);
        $this->hasWorkflow($entityId, static::$entity)->shouldReturn(true);
    }

    function it_knows_if_no_matching_workflow_exists(Workflow $workflow, EntityId $entityId)
    {
        $workflow->match($entityId, static::$entity)->willReturn(false);
        $this->hasWorkflow($entityId, static::$entity)->shouldReturn(false);
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
        EntityId $entityId
    ) {
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);

        $workflow->match($entityId, static::$entity)->willReturn(false);
        $this->handle($item)->shouldReturn(false);
    }

    function it_creates_handler_for_start_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $transitionHandlerFactory,
        EntityId $entityId,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler
    )
    {
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(false);

        $workflow->match($entityId, static::$entity)->willReturn(true);

        $entityId->getProviderName()->willReturn(static::ENTITY_PROVIDER_NAME);

        $transitionHandlerFactory->createTransitionHandler(
            $item,
            $workflow,
            Argument::any(),
            static::ENTITY_PROVIDER_NAME,
            $stateRepository
        )
            ->willReturn($transitionHandler);

        $this->handle($item)->shouldReturn($transitionHandler);
    }

    function it_creates_handler_for_ongoing_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $transitionHandlerFactory,
        EntityId $entityId,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler,
        Transition $transition,
        Step $step
    )
    {
        $step->getName()->willReturn('start');
        $step->isTransitionAllowed('next')->willReturn(true);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->match($entityId, static::$entity)->willReturn(true);
        $workflow->getStep('start')->willReturn($step);
        $workflow->getTransition('next')->willReturn($transition);
        $workflow->getName()->willReturn('workflow_a');

        $entityId->getProviderName()->willReturn(static::ENTITY_PROVIDER_NAME);

        $transitionHandlerFactory->createTransitionHandler(
            $item,
            $workflow,
            Argument::any(),
            static::ENTITY_PROVIDER_NAME,
            $stateRepository
        )
            ->willReturn($transitionHandler);

        $this->handle($item, 'next')->shouldReturn($transitionHandler);
    }


    function it_throws_than_matches_workflow_is_not_same_as_current(
        Workflow $workflow,
        Item $item,
        EntityId $entityId,
        Transition $transition,
        Step $step
    )
    {
        $step->getName()->willReturn('start');
        $step->isTransitionAllowed('next')->willReturn(true);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->match($entityId, static::$entity)->willReturn(true);
        $workflow->getStep('start')->willReturn($step);
        $workflow->getTransition('next')->willReturn($transition);
        $workflow->getName()->willReturn('workflow_b');

        $entityId->getProviderName()->willReturn(static::ENTITY_PROVIDER_NAME);
        $entityId->__toString()->willReturn(static::ENTITY_PROVIDER_NAME . '::' . static::ENTITY_ID);

        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\WorkflowException')
            ->duringHandle($item, 'next');
    }

    function it_creates_an_item(
        EntityId $entityId,
        EntityId $entityId,
        StateRepository $stateRepository,
        State $state
    ) {
        $stateRepository->find($entityId)->willReturn(array($state));

        $this->createItem($entityId, static::$entity)->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

}
