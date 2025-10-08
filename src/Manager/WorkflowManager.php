<?php

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
use Override;

use function sprintf;

/**
 * Class Manager handles a set of workflows.
 *
 * Usually there will a different workflow manager for different workflow types. The manager is the API entry point
 * when using the workflow API.
 */
final class WorkflowManager implements Manager
{
    /**
     * @param TransitionHandlerFactory $handlerFactory  The transition handler factory.
     * @param StateRepository          $stateRepository The state repository.
     * @param Workflow[]               $workflows       The set of managed workflows.
     */
    public function __construct(
        private readonly TransitionHandlerFactory $handlerFactory,
        private readonly StateRepository $stateRepository,
        private array $workflows = [],
    ) {
        Assertion::allIsInstanceOf($workflows, Workflow::class);
    }

    #[Override]
    public function handle(
        Item $item,
        string|null $transitionName = null,
        bool $changeWorkflow = false,
    ): TransitionHandler|null {
        $entity = $item->getEntity();

        if (! $this->hasWorkflow($item->getEntityId(), $entity)) {
            return null;
        }

        $workflow = $this->getWorkflowByItem($item);

        if ($this->hasWorkflowChanged($item, $workflow, ! $changeWorkflow) && $changeWorkflow) {
            $item->detach();
        }

        return $this->handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $item->getEntityId()->getProviderName(),
            $this->stateRepository,
        );
    }

    #[Override]
    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->workflows[] = $workflow;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws WorkflowNotFound When no supporting workflow is found.
     */
    #[Override]
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
     * {@inheritDoc}
     *
     * @throws WorkflowNotFound When no workflow with name is found.
     */
    #[Override]
    public function getWorkflowByName(string $name): Workflow
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->getName() === $name) {
                return $workflow;
            }
        }

        throw WorkflowNotFound::withName($name);
    }

    #[Override]
    public function getWorkflowByItem(Item $item): Workflow
    {
        $workflowName = $item->getWorkflowName();
        if ($workflowName !== null) {
            $workflow = $this->getWorkflowByName($workflowName);

            if ($workflow->supports($item->getEntityId(), $item->getEntity())) {
                return $workflow;
            }
        }

        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /** {@inheritDoc} */
    #[Override]
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
     * {@inheritDoc}
     */
    #[Override]
    public function getWorkflows(): iterable
    {
        return $this->workflows;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
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
     */
    private function hasWorkflowChanged(Item $item, Workflow $workflow, bool $throw = true): bool
    {
        if ($item->isWorkflowStarted() && $item->getWorkflowName() !== $workflow->getName()) {
            $message = sprintf(
                'Item "%s" already process workflow "%s" and cannot be handled by "%s"',
                $item->getEntityId(),
                (string) $item->getWorkflowName(),
                $workflow->getName(),
            );

            if ($throw) {
                throw new FlowException($message);
            }

            return true;
        }

        return false;
    }
}
