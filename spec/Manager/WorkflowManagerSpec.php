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

namespace spec\Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ManagerSpec
 *
 * @package spec\Netzmacht\Contao\Workflow
 */
class WorkflowManagerSpec extends ObjectBehavior
{
    const ENTITY_PROVIDER_NAME = 'provider_name';

    const ENTITY_ID = 5;

    protected static $entity = ['id' => 5];

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

        $this->beConstructedWith($transitionHandlerFactory, $stateRepository, [$workflow]);
    }

    function it_gets_workflow(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->supports($entityId, static::$entity)->willReturn(true);

        $this->getWorkflow($entityId, static::$entity)->shouldReturn($workflow);
    }

    function it_gets_workflow_by_item(Workflow $workflow, Item $item)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $item->getWorkflowName()->willReturn('workflow_a');
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);

        $workflow->supports($entityId, static::$entity)->willReturn(true);

        $this->getWorkflowByItem($item)->shouldReturn($workflow);
    }

    function it_adds_workflow(Workflow $anotherWorkflow)
    {
        $anotherWorkflow->getName()->willReturn('another');

        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflowByName('another')->shouldReturn($anotherWorkflow);
    }

    function it_returns_false_if_no_supported_workflow_found(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->supports($entityId, static::$entity)->willReturn(false);
    }

    function it_throws_workflow_not_found_when_specific_workflow_not_exists()
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $this->shouldThrow(WorkflowNotFound::class)
            ->during('getWorkflowByName', [$entityId, static::$entity]);
    }

    function it_knows_if_matching_workflow_exists(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->supports($entityId, static::$entity)->willReturn(true);
        $this->hasWorkflow($entityId, static::$entity)->shouldReturn(true);
    }

    function it_knows_if_no_matching_workflow_exists(Workflow $workflow)
    {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $workflow->supports($entityId, static::$entity)->willReturn(false);
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

        $workflow->supports($entityId, static::$entity)->willReturn(false);
        $this->handle($item)->shouldReturn(null);
    }

    function it_creates_handler_for_start_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $transitionHandlerFactory,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler
    ) {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $item->getWorkflowName()->willReturn('workflow_a');
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(false);

        $workflow->supports($entityId, static::$entity)->willReturn(true);

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
    ) {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $step->getName()->willReturn('start');
        $step->isTransitionAllowed('next')->willReturn(true);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->supports($entityId, static::$entity)->willReturn(true);
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
    ) {
        $entityId = EntityId::fromProviderNameAndId(static::ENTITY_PROVIDER_NAME, static::ENTITY_ID);

        $step->getName()->willReturn('start');
        $step->isTransitionAllowed('next')->willReturn(true);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(static::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->supports($entityId, static::$entity)->willReturn(true);
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

        $stateRepository->find($entityId)->willReturn([$state]);

        $this->createItem($entityId, static::$entity)->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

}
