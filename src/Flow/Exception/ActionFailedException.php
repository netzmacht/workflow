<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Exception;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;

use function end;
use function explode;
use function sprintf;
use function trim;

/**
 * Class TransactionActionFailed is thrown then a transaction action failed.
 */
class ActionFailedException extends FlowException
{
    /**
     * The action name.
     */
    private string|null $actionName = null;

    /**
     * Additional error collection.
     */
    private ErrorCollection|null $errorCollection = null;

    /**
     * Create exception for with an action name.
     *
     * @param string               $actionName      The action name.
     * @param ErrorCollection|null $errorCollection Additional error collection.
     *
     * @return ActionFailedException
     */
    public static function namedAction(string $actionName, ErrorCollection|null $errorCollection = null): self
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
    public static function action(Action $action, ErrorCollection|null $errorCollection = null): self
    {
        if ($action instanceof Base) {
            $actionName = $action->getLabel();
        } else {
            $parts      = explode('\\', trim($action::class, '\\'));
            $actionName = end($parts);
        }

        return self::namedAction($actionName, $errorCollection);
    }

    /**
     * Get the action name.
     */
    public function actionName(): string|null
    {
        return $this->actionName;
    }

    /**
     * Get the error collection.
     */
    public function errorCollection(): ErrorCollection|null
    {
        return $this->errorCollection;
    }
}
