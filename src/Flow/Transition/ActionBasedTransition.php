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

namespace Netzmacht\Workflow\Flow\Transition;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ActionBasedTransition extends AbstractTransition
{
    /**
     * Actions which will be executed during the transition.
     *
     * @var Action[]
     */
    private $actions = [];

    /**
     * Post actions which will be executed when new step is reached.
     *
     * @var Action[]
     */
    private $postActions = [];

    /**
     * Add an action to the transition.
     *
     * @param Action $action The added action.
     *
     * @return $this
     */
    public function addAction(Action $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Get all actions.
     *
     * @return Action[]|iterable
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    /**
     * Add an post action to the transition.
     *
     * @param Action $action The added action.
     *
     * @return $this
     */
    public function addPostAction(Action $action): self
    {
        $this->postActions[] = $action;

        return $this;
    }

    /**
     * Get all post actions.
     *
     * @return Action[]|iterable
     */
    public function getPostActions(): iterable
    {
        return $this->postActions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPayloadProperties(Item $item): array
    {
        if (empty($this->actions)) {
            return [];
        }

        return array_merge(
            ... array_map(
                function (Action $action) use ($item) {
                    return $action->getRequiredPayloadProperties($item);
                },
                $this->actions
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Item $item, Context $context): bool
    {
        $validated = true;

        foreach ($this->actions as $action) {
            $validated = $validated && $action->validate($item, $context);
        }

        return $validated;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Item $item, Context $context) : State
    {
        $success = $this->executeActions($item, $context);

        if ($item->isWorkflowStarted()) {
            $state = $item->transit($this, $context, $success);
        } else {
            $state = $item->start($this, $context, $success);
        }

        $this->executePostActions($item, $context);

        return $state;
    }

    /**
     * Execute all actions.
     *
     * @param Item    $item    The workflow item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function executeActions(Item $item, Context $context): bool
    {
        return $this->doExecuteActions($item, $context, $this->actions);
    }

    /**
     * Execute all actions.
     *
     * @param Item    $item    The workflow item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function executePostActions(Item $item, Context $context): bool
    {
        return $this->doExecuteActions($item, $context, $this->postActions);
    }

    /**
     * Execute the actions.
     *
     * @param Item     $item    Workflow item.
     * @param Context  $context Condition context.
     * @param Action[] $actions Action to execute.
     *
     * @return bool
     */
    private function doExecuteActions(Item $item, Context $context, $actions): bool
    {
        $success = $this->isAllowed($item, $context);

        if ($success) {
            try {
                foreach ($actions as $action) {
                    $action->transit($this, $item, $context);
                }
            } catch (ActionFailedException $e) {
                $params = [
                    'exception' => $e->getMessage(),
                    'action'    => $e->actionName()
                ];
                $context->addError('transition.action.failed', $params, $e->errorCollection());

                return false;
            }
        }

        return $success && !$context->getErrorCollection()->hasErrors();
    }
}
