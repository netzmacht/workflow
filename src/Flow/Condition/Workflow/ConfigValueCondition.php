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

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * This condition checks a config parameter value
 *
 * @package Netzmacht\Contao\Workflow\Condition\Workflow
 */
class ConfigValueCondition implements Condition
{
    /**
     * Name of the config parameter.
     *
     * @var string
     */
    private $name;

    /**
     * Value of the config parameter.
     *
     * @var mixed
     */
    private $value;

    /**
     * If true a strict comparison is made.
     *
     * @var bool
     */
    private $strict;

    /**
     * ConfigCondition constructor.
     *
     * @param string $name   Name of the config parameter.
     * @param mixed  $value  Value of the config parameter.
     * @param bool   $strict If true a strict comparison is made.
     */
    public function __construct(string $name, $value, bool $strict = false)
    {
        $this->name   = $name;
        $this->value  = $value;
        $this->strict = $strict;
    }

    /**
     * {@inheritDoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        $value = $workflow->getConfigValue($this->name);

        if ($this->strict) {
            return $this->value === $value;
        }

        return $this->value == $value;
    }
}
