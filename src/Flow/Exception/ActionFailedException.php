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

namespace Netzmacht\Workflow\Flow\Exception;

use function end;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;

/**
 * Class TransactionActionFailed is thrown then a transaction action failed.
 *
 * @package Netzmacht\Workflow\Flow\Transition
 */
class ActionFailedException extends FlowException
{
    /**
     * The action name.
     *
     * @var string|null
     */
    private $actionName;

    /**
     * Additional error collection.
     *
     * @var ErrorCollection|null
     */
    private $errorCollection;

    /**
     * Create exception for with an action name.
     *
     * @param string               $actionName      The action name.
     * @param ErrorCollection|null $errorCollection Additional error collection.
     *
     * @return ActionFailedException
     */
    public static function namedAction(string $actionName, ?ErrorCollection $errorCollection = null): self
    {
        $exception                  = new self(sprintf('Execution of action "%s" failed.', $actionName));
        $exception->actionName      = $actionName;
        $exception->errorCollection = $errorCollection;

        return $exception;
    }

    /**
     * Create exception for an action.
     *
     * @param Action               $action          The action.
     * @param ErrorCollection|null $errorCollection Additional error collection.
     *
     * @return ActionFailedException
     */
    public static function action(Action $action, ?ErrorCollection $errorCollection = null): self
    {
        if ($action instanceof Base) {
            $actionName = $action->getLabel();
        } else {
            $parts      = explode('\\', trim(get_class($action), '\\'));
            $actionName = end($parts);
        }

        return self::namedAction($actionName, $errorCollection);
    }

    /**
     * Get the action name.
     *
     * @return string|null
     */
    public function actionName(): ?string
    {
        return $this->actionName;
    }

    /**
     * Get the error collection.
     *
     * @return ErrorCollection|null
     */
    public function errorCollection(): ?ErrorCollection
    {
        return $this->errorCollection;
    }
}
