<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Exception;

use Netzmacht\Workflow\Exception\WorkflowException;
use RuntimeException;

class FlowException extends RuntimeException implements WorkflowException
{
}
