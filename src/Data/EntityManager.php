<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Data;

use InvalidArgumentException;

/**
 * Interface EntityManager create entity repositories.
 */
interface EntityManager
{
    /**
     * Create an entity repository.
     *
     * @param string $providerName The provider name.
     *
     * @throws InvalidArgumentException If repository could not be created.
     */
    public function getRepository(string $providerName): EntityRepository;
}
