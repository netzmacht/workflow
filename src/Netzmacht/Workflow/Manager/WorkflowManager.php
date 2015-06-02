<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Manager;

use Assert\Assertion;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class Manager handles a set of workflows.
 *
 * Usually there will a different workflow manager for different workflow types. The manager is the API entry point
 * when using the workflow API.
 *
 * @package Netzmacht\Workflow
 */
class WorkflowManager implements \Netzmacht\Workflow\Manager\Manager
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
        $workflows = array()
    ) {
        Assertion::allIsInstanceOf($workflows, 'Netzmacht\Workflow\Flow\Workflow');

        $this->workflows       = $workflows;
        $this->handlerFactory  = $handlerFactory;
        $this->stateRepository = $stateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Item $item, $transitionName = null)
    {
        $entity   = $item->getEntity();
        $workflow = $this->getWorkflow($item->getEntityId(), $entity);

        if (!$workflow) {
            return false;
        }

        $this->guardSameWorkflow($item, $workflow);

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
    public function addWorkflow(Workflow $workflow)
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(EntityId $entityId, $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entityId, $entity)) {
                return $workflow;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByName($name)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->getName() == $name) {
                return $workflow;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByItem(Item $item)
    {
        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritdoc}
     */
    public function hasWorkflow(EntityId $entityId, $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entityId, $entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * {@inheritdoc}
     */
    public function createItem(EntityId $entityId, $entity)
    {
        $stateHistory = $this->stateRepository->find($entityId);

        return Item::reconstitute($entityId, $entity, $stateHistory);
    }

    /**
     * Guard that already started workflow is the same which is tried to be ran now.
     *
     * @param Item     $item     Current workflow item.
     * @param Workflow $workflow Selected workflow.
     *
     * @throws WorkflowException If item workflow is not the same as current workflow.
     *
     * @return void
     */
    private function guardSameWorkflow(Item $item, Workflow $workflow)
    {
        if ($item->isWorkflowStarted() && $item->getWorkflowName() != $workflow->getName()) {
            $message = sprintf(
                'Item "%s" already process workflow "%s" and cannot be handled by "%s"',
                $item->getEntityId(),
                $item->getWorkflowName(),
                $workflow->getName()
            );

            throw new WorkflowException($message);
        }
    }
}
