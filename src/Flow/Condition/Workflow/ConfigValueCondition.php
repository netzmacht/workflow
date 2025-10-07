<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use Override;

/**
 * This condition checks a config parameter value
 */
class ConfigValueCondition implements Condition
{
    /**
     * Name of the config parameter.
     */
    private string $name;

    /**
     * Value of the config parameter.
     */
    private mixed $value;

    /**
     * If true a strict comparison is made.
     */
    private bool $strict;

    /**
     * @param string $name   Name of the config parameter.
     * @param mixed  $value  Value of the config parameter.
     * @param bool   $strict If true a strict comparison is made.
     */
    public function __construct(string $name, mixed $value, bool $strict = false)
    {
        $this->name   = $name;
        $this->value  = $value;
        $this->strict = $strict;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        $value = $workflow->getConfigValue($this->name);

        if ($this->strict) {
            return $this->value === $value;
        }

        return $this->value === $value;
    }
}
