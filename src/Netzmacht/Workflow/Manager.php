<?php

namespace Netzmacht\Workflow;

use Assert\Assertion;
use Netzmacht\Workflow\Data\Entity;
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
class Manager
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
     * @param Workflow[]|array         $workflows       The set of managed workflows.
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        array $workflows = array()
    ) {
        Assertion::allIsInstanceOf($workflows, 'Netzmacht\Workflow\Flow\Workflow');

        $this->workflows       = $workflows;
        $this->handlerFactory  = $handlerFactory;
        $this->stateRepository = $stateRepository;
    }

    /**
     * Handle a workflow transition of an entity will createRepository a transition handler.
     *
     * If no matching workflow definition is found false will be returned.
     *
     * @param Item   $item           The current workflow item.
     * @param string $transitionName Transition name, required if workflow has already started.
     *
     * @throws WorkflowException If something went wrong.
     *
     * @return bool|TransitionHandler
     */
    public function handle(Item $item, $transitionName = null)
    {
        $entity   = $item->getEntity();
        $workflow = $this->getWorkflow($entity);

        if (!$workflow) {
            return false;
        }

        $this->guardSameWorkflow($item, $workflow);

        $handler = $this->handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $entity->getEntityId()->getProviderName(),
            $this->stateRepository
        );

        return $handler;
    }


    /**
     * Add a workflow to the manager.
     *
     * @param Workflow $workflow The workflow being added.
     *
     * @return $this
     */
    public function addWorkflow(Workflow $workflow)
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * Get a workflow for the given entity.
     *
     * @param Entity $entity The entity.
     *
     * @return Workflow|bool
     */
    public function getWorkflow(Entity $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entity)) {
                return $workflow;
            }
        }

        return false;
    }

    /**
     * Get Workflow by its name.
     *
     * @param string $name Name of workflow.
     *
     * @return bool|Workflow
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
     * Consider if entity has an workflow.
     *
     * @param Entity $entity The entity.
     *
     * @return bool
     */
    public function hasWorkflow(Entity $entity)
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->match($entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all registered workflows.
     *
     * @return Workflow[]
     */
    public function getWorkflows()
    {
        return $this->workflows;
    }

    /**
     * Create the item for an entity.
     *
     * @param Entity $entity Current entity.
     *
     * @return Item
     */
    public function createItem(Entity $entity)
    {
        $stateHistory = $this->stateRepository->find($entity->getEntityId());

        return Item::reconstitute($entity, $stateHistory);
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
                $item->getEntity()->getEntityId(),
                $item->getWorkflowName(),
                $workflow->getName()
            );

            throw new WorkflowException($message);
        }
    }
}
