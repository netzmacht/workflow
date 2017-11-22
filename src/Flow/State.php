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

namespace Netzmacht\Workflow\Flow;

use DateTimeImmutable;
use Netzmacht\Workflow\Data\EntityId;

/**
 * Class State stores information of a current state of an entity.
 *
 * @package Netzmacht\Workflow\Flow
 */
class State
{
    /**
     * The state id.
     *
     * @var int
     */
    private $stateId;

    /**
     * The entity id.
     *
     * @var EntityId
     */
    private $entityId;

    /**
     * Store if transition was successful.
     *
     * @var bool
     */
    private $successful;

    /**
     * The last transition.
     *
     * @var string
     */
    private $transitionName;

    /**
     * The current step.
     *
     * @var string
     */
    private $stepName;

    /**
     * Date being stored.
     *
     * @var array
     */
    private $data = array();

    /**
     * Date when state was reached.
     *
     * @var DateTimeImmutable
     */
    private $reachedAt;

    /**
     * List of errors.
     *
     * @var array
     */
    private $errors;

    /**
     * Name of the workflow.
     *
     * @var string
     */
    private $workflowName;

    /**
     * Construct.
     *
     * @param EntityId          $entityId       The entity id.
     * @param string            $workflowName   Workflow name.
     * @param string            $transitionName The transition executed to reach the step.
     * @param string            $stepToName     The step reached after transition.
     * @param bool              $successful     Consider if transition was successful.
     * @param array             $data           Stored data.
     * @param DateTimeImmutable $reachedAt      Time when state was reached.
     * @param array             $errors         List of errors.
     * @param int               $stateId        The state id of a persisted state.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityId $entityId,
        string $workflowName,
        string $transitionName,
        string $stepToName,
        bool $successful,
        array $data,
        DateTimeImmutable $reachedAt,
        array $errors = array(),
        int $stateId = null
    ) {
        $this->entityId       = $entityId;
        $this->workflowName   = $workflowName;
        $this->transitionName = $transitionName;
        $this->stepName       = $stepToName;
        $this->successful     = $successful;
        $this->data           = $data;
        $this->reachedAt      = $reachedAt;
        $this->errors         = $errors;
        $this->stateId        = $stateId;
    }

    /**
     * Create an initial state.
     *
     * @param EntityId   $entityId   The entity id.
     * @param Transition $transition The current executed transition.
     * @param Context    $context    The context.
     * @param bool       $success    Success state.
     *
     * @return State
     */
    public static function start(
        EntityId $entityId,
        Transition $transition,
        Context $context,
        $success
    ) {
        $state = new State(
            $entityId,
            $transition->getWorkflow()->getName(),
            $transition->getName(),
            $transition->getStepTo()->getName(),
            $success,
            $context->getProperties()->toArray(),
            new \DateTimeImmutable(),
            $context->getErrorCollection()->toArray()
        );

        return $state;
    }

    /**
     * Get step name.
     *
     * @return string
     */
    public function getStepName(): string
    {
        return $this->stepName;
    }

    /**
     * Get transition name.
     *
     * @return string
     */
    public function getTransitionName(): string
    {
        return $this->transitionName;
    }

    /**
     * Get the workflow name.
     *
     * @return string
     */
    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    /**
     * Get state data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get reached at time.
     *
     * @return DateTimeImmutable
     */
    public function getReachedAt(): \DateTimeImmutable
    {
        return $this->reachedAt;
    }

    /**
     * Consider if state is successful.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * Get the entity id.
     *
     * @return EntityId
     */
    public function getEntityId(): EntityId
    {
        return $this->entityId;
    }

    /**
     * Get error messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get state id.
     *
     * @return int|null
     */
    public function getStateId():? int
    {
        return $this->stateId;
    }

    /**
     * Transit to a new state.
     *
     * @param Transition $transition The transition being performed.
     * @param Context    $context    The transition context.
     * @param bool       $success    The success state.
     *
     * @return State
     */
    public function transit(
        Transition $transition,
        Context $context,
        bool $success = true
    ): State {
        $dateTime = new DateTimeImmutable();
        $stepName = $success ? $transition->getStepTo()->getName() : $this->stepName;

        return new static(
            $this->entityId,
            $this->workflowName,
            $transition->getName(),
            $stepName,
            $success,
            $context->getProperties()->toArray(),
            $dateTime,
            $context->getErrorCollection()->getErrors()
        );
    }
}
