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

use Netzmacht\Workflow\Data\Entity;

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
     * @var State[]|array
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
     * Start a new workflow.
     *
     * @param Entity $entity The entity for which the workflow is started.
     *
     * @return Item
     */
    public static function start(Entity $entity)
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
        $item = self::start($entity);

        // replay states
        foreach ($stateHistory as $state) {
            $item->transit($state);
        }

        return $item;
    }

    /**
     * Transits to a new state.
     *
     * @param \Netzmacht\Workflow\Flow\State $state The state being created.
     *
     * @return $this
     */
    public function transit(State $state)
    {
        // only change current step if transition was successful
        if ($state->isSuccessful()) {
            $this->currentStepName = $state->getStepName();
            $this->workflowName    = $state->getWorkflowName();
        }

        $this->stateHistory[] = $state;

        return $this;
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
     * Get the state history.
     *
     * @return array|\Netzmacht\Workflow\Flow\State[]
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
     * @return bool|\Netzmacht\Workflow\Flow\State
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
}
