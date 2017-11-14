<?php

namespace spec\Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
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
        $workflow->getName()->willReturn('workflow_a');

        $this->beConstructedWith($transitionHandlerFactory, $stateRepository, array($workflow));
    }

    function it_gets_workflow(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->match($entityId, static::$entity)->willReturn(true);

        $this->getWorkflow($entityId, static::$entity)->shouldReturn($workflow);
    }

    function it_gets_workflow_by_item(Workflow $workflow, Item $item)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

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

    function it_returns_false_if_no_workflow_found(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->match($entityId, static::$entity)->willReturn(false);

        $this->getWorkflow($entityId, static::$entity)->shouldReturn(false);
    }

    function it_knows_if_matching_workflow_exists(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->match($entityId, static::$entity)->willReturn(true);
        $this->hasWorkflow($entityId, static::$entity)->shouldReturn(true);
    }

    function it_knows_if_no_matching_workflow_exists(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

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
        Item $item
    ) {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);

        $workflow->match($entityId, static::$entity)->willReturn(false);
        $this->handle($item)->shouldReturn(false);
    }

    function it_creates_handler_for_start_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $transitionHandlerFactory,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler
    )
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(false);

        $workflow->match($entityId, static::$entity)->willReturn(true);

        $transitionHandlerFactory->createTransitionHandler(
            $item,
            $workflow,
            Argument::any(),
            static::ENTITY_PROVIDER_NAME,
            $stateRepository
        )->willReturn($transitionHandler);

        $this->handle($item)->shouldReturn($transitionHandler);
    }

    function it_creates_handler_for_ongoing_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $transitionHandlerFactory,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler,
        Transition $transition,
        Step $step
    )
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

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
        Transition $transition,
        Step $step
    )
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

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

        $this
            ->shouldThrow('Netzmacht\Workflow\Exception\WorkflowException')
            ->duringHandle($item, 'next');
    }

    function it_creates_an_item(
        StateRepository $stateRepository,
        State $state
    ) {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $state->getStepName()->willReturn('step');
        $state->getWorkflowName()->willReturn('workflow');
        $state->isSuccessful()->willReturn(true);

        $stateRepository->find($entityId)->willReturn(array($state));

        $this->createItem($entityId, static::$entity)->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

}
