<?php

/**
 * This Contao-Workflow extension allows the definition of workflow process for entities from different providers. This
 * extension is a workflow framework which can be used from other extensions to provide their custom workflow handling.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\ContaoWorkflowBundle\Workflow\Definition\Loader\WorkflowLoader;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
use Netzmacht\Workflow\Manager\Manager;
use Netzmacht\Workflow\Manager\WorkflowManager;

/**
 * Class ChangeWorkflowTransitionHandler.
 *
 * @package Netzmacht\Workflow
 */
class ChangeWorkflowTransitionHandler implements TransitionHandler
{
    /** @var TransitionHandler */
    private $transitionHandler;

    /** @var bool */
    private $executeWorkflowTransition;

    /** @var Manager */
    private $workflowManager;

    /** @var Step|null */
    private $expectedStepTo;

    public function __construct(Manager $workflowManager, TransitionHandler $transitionHandler)
    {
        $this->transitionHandler = $transitionHandler;
        $this->workflowManager = $workflowManager;
        $this->expectedStepTo = $transitionHandler->getTransition()->getStepTo();

        $this->executeWorkflowTransition = ($this->expectedStepTo && $this->expectedStepTo->getTriggerWorkflow());
    }

    public function getWorkflow(): Workflow
    {
        return $this->transitionHandler->getWorkflow();
    }

    public function getItem(): Item
    {
        return $this->transitionHandler->getItem();
    }

    public function getTransition(): Transition
    {
        return $this->transitionHandler->getTransition();
    }

    public function getCurrentStep(): ?Step
    {
        return $this->transitionHandler->getCurrentStep();
    }

    public function isWorkflowStarted(): bool
    {
        return $this->transitionHandler->isWorkflowStarted();
    }

    public function getRequiredPayloadProperties(): array
    {
        if (!$this->nextWorkflowTransitionHandler) {
            return $this->transitionHandler->getRequiredPayloadProperties();
        }

        return array_merge(
            $this->transitionHandler->getRequiredPayloadProperties(),
            $this->nextWorkflowTransitionHandler->getRequiredPayloadProperties()
        );
    }

    public function isAvailable(): bool
    {
        if (! $this->transitionHandler->isAvailable()) {
            return false;
        }

        if ($this->nextWorkflowTransitionHandler) {
            return $this->nextWorkflowTransitionHandler->isAvailable();
        }

        return true;
    }

    public function getContext(): Context
    {
        return $this->transitionHandler->getContext();
    }

    public function validate(array $payload = []): bool
    {
        if (!$this->transitionHandler->validate($payload)) {
            return false;
        }

        if ($this->nextWorkflowTransitionHandler) {
            return $this->nextWorkflowTransitionHandler->validate($payload);
        }

        return true;
    }

    public function transit(): State
    {
        $state = $this->transitionHandler->transit();

        if (!$state->isSuccessful() || !$this->executeWorkflowTransition) {
            return $state;
        }

        if ($this->expectedStepTo->getName() !== $state->getStepName()) {
            throw new FlowException('Unexpected step reached');
        }

        $workflow = $this->workflowManager->getWorkflowByName('workflow_' . $this->expectedStepTo->getTriggerWorkflow());

        $nextWorkflowTransitionHandler = $this->workflowManager->createTransitionHandler(
            $workflow,
            $this->getItem(),
            $workflow->getStartTransition()->getName(),
            true
        );

        return $nextWorkflowTransitionHandler->transit();
    }
}