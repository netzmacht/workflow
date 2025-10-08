<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Exception;

use Exception;
use Netzmacht\Workflow\Data\EntityId;
use RuntimeException;
use Throwable;

use function sprintf;

final class WorkflowNotFound extends RuntimeException implements WorkflowException
{
    /**
     * Create exception with the workflow name.
     *
     * @param string    $workflowName Current workflow name.
     * @param int       $code         Error code.
     * @param Exception $previous     Previous thrown exception.
     */
    public static function withName(string $workflowName, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(sprintf('Workflow "%s" not found.', $workflowName), $code, $previous);
    }

    /**
     * Create exception with the workflow name.
     *
     * @param EntityId  $entityId Entity id.
     * @param int       $code     Error code.
     * @param Exception $previous Previous thrown exception.
     */
    public static function forEntity(EntityId $entityId, int $code = 0, Throwable|null $previous = null): self
    {
        return new self(sprintf('No workflow found for entity "%s".', $entityId), $code, $previous);
    }
}
