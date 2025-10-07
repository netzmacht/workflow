<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use Override;

/**
 * Class ProviderTypeCondition check if entity matches a specific provider.
 */
class ProviderNameCondition implements Condition
{
    /**
     * Provider name to check against.
     */
    private string $providerName;

    public function __construct(string $providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * Get the provider name to check against.
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        if ($this->providerName) {
            return $entityId->getProviderName() === $this->providerName;
        }

        return $entityId->getProviderName() === $workflow->getProviderName();
    }
}
