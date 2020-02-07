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

declare(strict_types=1);

namespace Netzmacht\Workflow\Manager;

use Assert\Assertion;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;

/**
 * Class Manager handles a set of workflows.
 *
 * Usually there will a different workflow manager for different workflow types. The manager is the API entry point
 * when using the workflow API.
 *
 * @package Netzmacht\Workflow
 */
class WorkflowManager implements Manager
{
    /**
     * The state repository.
     *
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * A set of workflows.
     *
     * @var Workflow[]
     */
    private $workflows;

    /**
     * A Transition handler factory.
     *
     * @var TransitionHandlerFactory
     */
    private $handlerFactory;

    /**
     * Construct.
     *
     * @param TransitionHandlerFactory $handlerFactory  The transition handler factory.
     * @param StateRepository          $stateRepository The state repository.
     * @param Workflow[]               $workflows       The set of managed workflows.
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        $workflows = []
    ) {
        Assertion::allIsInstanceOf($workflows, Workflow::class);

        $this->workflows       = $workflows;
        $this->handlerFactory  = $handlerFactory;
        $this->stateRepository = $stateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Item $item, string $transitionName = null, bool $changeWorkflow = false): ?TransitionHandler
    {
        $entity = $item->getEntity();

        if (!$this->hasWorkflow($item->getEntityId(), $entity)) {
            return null;
        }

        $workflow = $this->getWorkflowByItem($item);

        if ($this->hasWorkflowChanged($item, $workflow, !$changeWorkflow) && $changeWorkflow) {
            $item->detach();
        }

        $handler = $this->handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $item->getEntityId()->getProviderName(),
            $this->stateRepository
        );

        return $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws WorkflowNotFound When no supporting workflow is found.
     */
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->supports($entityId, $entity)) {
                return $workflow;
            }
        }

        throw WorkflowNotFound::forEntity($entityId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws WorkflowNotFound When no workflow with name is found.
     */
    public function getWorkflowByName(string $name): Workflow
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->getName() === $name) {
                return $workflow;
            }
        }

        throw WorkflowNotFound::withName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByItem(Item $item): Workflow
    {
        if ($item->getWorkflowName()) {
            $workflow = $this->getWorkflowByName($item->getWorkflowName());

            if ($workflow->supports($item->getEntityId(), $item->getEntity())) {
                return $workflow;
            }
        }

        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritdoc}
     */
    public function hasWorkflow(EntityId $entityId, $entity): bool
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->supports($entityId, $entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflows(): iterable
    {
        return $this->workflows;
    }

    /**
     * {@inheritdoc}
     */
    public function createItem(EntityId $entityId, $entity): Item
    {
        $stateHistory = $this->stateRepository->find($entityId);

        return Item::reconstitute($entityId, $entity, $stateHistory);
    }

    /**
     * Guard that already started workflow is the same which is tried to be ran now.
     *
     * @param Item     $item     Current workflow item.
     * @param Workflow $workflow Selected workflow.
     * @param bool     $throw    If true an error is thrown.
     *
     * @throws FlowException If item workflow is not the same as current workflow.
     *
     * @return bool
     */
    private function hasWorkflowChanged(Item $item, Workflow $workflow, bool $throw = true): bool
    {
        if ($item->isWorkflowStarted() && $item->getWorkflowName() != $workflow->getName()) {
            $message = sprintf(
                'Item "%s" already process workflow "%s" and cannot be handled by "%s"',
                $item->getEntityId(),
                $item->getWorkflowName(),
                $workflow->getName()
            );

            if ($throw) {
                throw new FlowException($message);
            }

            return true;
        }

        return false;
    }
}
