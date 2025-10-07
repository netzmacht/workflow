<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Exception;

use Throwable;

use function sprintf;

/**
 * Class TransitionNotFoundException is thrown, then transition was not found.
 */
class TransitionNotFound extends FlowException
{
    /**
     * Construct.
     *
     * @param string         $transitionName The not found transition name.
     * @param string         $workflowName   Current workflow name.
     * @param int            $code           Error code.
     * @param Throwable|null $previous       Previous thrown exception.
     */
    public static function withName(
        string $transitionName,
        string $workflowName,
        int $code = 0,
        Throwable|null $previous = null,
    ): TransitionNotFound {
        return new self(
            sprintf('Transition "%s" not found in workflow "%s"', $transitionName, $workflowName),
            $code,
            $previous,
        );
    }
}
