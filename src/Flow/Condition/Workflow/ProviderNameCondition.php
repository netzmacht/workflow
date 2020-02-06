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
 * Class ProviderTypeCondition check if entity matches a specific provider.
 *
 * @package Netzmacht\Workflow\Flow\Workflow\Condition
 */
class ProviderNameCondition implements Condition
{
    /**
     * Provider name to check against.
     *
     * @var string
     */
    private $providerName;

    /**
     * ProviderNameCondition constructor.
     *
     * @param string $providerName
     */
    public function __construct(string $providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * Get the provider name to check against.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        if ($this->providerName) {
            return $entityId->getProviderName() === $this->providerName;
        }

        return $entityId->getProviderName() === $workflow->getProviderName();
    }
}
