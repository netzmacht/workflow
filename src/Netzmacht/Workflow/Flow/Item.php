<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow;

use Assert\Assertion;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;

/**
 * Class Item stores workflow related data of an entity. It knows the state history and the current state.
 *
 * @package Netzmacht\Workflow
 */
class Item
{
    /**
     * Workflow name.
     *
     * @var string
     */
    private $workflowName;

    /**
     * Current step name.
     *
     * @var string
     */
    private $currentStepName;

    /**
     * State the items already had.
     *
     * @var State[]
     */
    private $stateHistory;

    /**
     * Workflow entity.
     *
     * @var Entity
     */
    private $entity;

    /**
     * Construct. Do not used constructor. Use named constructor static methods.
     *
     * @param Entity $entity The entity for which the workflow is started.
     */
    protected function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Initialize a new workflow item.
     *
     * It is called before the workflow is started.
     *
     * @param Entity $entity The entity for which the workflow is started.
     *
     * @return Item
     */
    public static function initialize(Entity $entity)
    {
        return new Item($entity);
    }

    /**
     * Restore an existing item.
     *
     * @param Entity        $entity       The entity.
     * @param State[]|array $stateHistory Set or already passed states.
     *
     * @return Item
     */
    public static function reconstitute(Entity $entity, array $stateHistory)
    {
        Assertion::allIsInstanceOf($stateHistory, 'Netzmacht\Workflow\Flow\State');

        $item = self::initialize($entity);

        // replay states
        foreach ($stateHistory as $state) {
            $item->apply($state);
        }

        return $item;
    }

    /**
     * Start an item and return current state.
     *
     * @param Transition      $transition      The transition being executed.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     * @param bool            $success         The transition success.
     *
     * @return State
     *
     * @throws WorkflowException
     */
    public function start(
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        $success
    ) {
        $this->guardNotStarted();

        $state = State::start($this->entity, $transition, $context, $errorCollection, $success);
        $this->apply($state);

        return $this;
    }

    /**
     * Transits to a new state and return it.
     *
     * @param Transition      $transition      The transition being executed.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     * @param bool            $success         The transition success.
     *
     * @throws WorkflowException
     *
     * @return State
     */
    public function transit(
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        $success
    ) {
        $this->guardStarted();

        $state = $this->getLatestState();
        $state = $state->transit($transition, $context, $errorCollection, $success);

        $this->apply($state);

        return $state;
    }

    /**
     * Get the name of the current step.
     *
     * @return string
     */
    public function getCurrentStepName()
    {
        return $this->currentStepName;
    }

    /**
     * Get the entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the state history of the workflow item.
     *
     * @return State[]
     */
    public function getStateHistory()
    {
        return $this->stateHistory;
    }

    /**
     * Get latest successful state.
     *
     * @param bool $successfulOnly Return only success ful steps.
     *
     * @return bool|State
     */
    public function getLatestState($successfulOnly = true)
    {
        if (!$successfulOnly) {
            return end($this->stateHistory);
        }

        for ($index = (count($this->stateHistory) - 1); $index >= 0; $index--) {
            if ($this->stateHistory[$index]->isSuccessful()) {
                return $this->stateHistory[$index];
            }
        }

        return false;
    }

    /**
     * Get name of the workflow.
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Consider if workflow has started.
     *
     * @return bool
     */
    public function isWorkflowStarted()
    {
        return !empty($this->currentStepName);
    }

    /**
     * Guard that workflow of item was not already started.
     *
     * @throws WorkflowException If item workflow process was already started.
     */
    private function guardNotStarted()
    {
        if ($this->isWorkflowStarted()) {
            throw new WorkflowException('Item is already started.');
        }
    }

    /**
     * Guard that workflow of item is started.
     *
     * @throws WorkflowException If item workflow process was not started.
     */
    private function guardStarted()
    {
        if (!$this->isWorkflowStarted()) {
            throw new WorkflowException('Item has not started yet.');
        }
    }


    /**
     * Apply a new state.
     *
     * @param State $state
     */
    private function apply(State $state)
    {
        // only change current step if transition was successful
        if ($state->isSuccessful()) {
            $this->currentStepName = $state->getStepName();
            $this->workflowName    = $state->getWorkflowName();
        }
        elseif (!$this->isWorkflowStarted()) {
            $this->workflowName    = $state->getWorkflowName();
        }

        $this->stateHistory[] = $state;
    }
}
