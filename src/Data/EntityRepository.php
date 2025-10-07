<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Data;

/**
 * Interface EntityRepository describes the repository which stores the items.
 */
interface EntityRepository
{
    /**
     * Find an entity by id.
     *
     * @param mixed $entityId The Entity id.
     */
    public function find(mixed $entityId): mixed;

    /**
     * Find multiple entities by a specification.
     *
     * @param Specification $specification The specification.
     *
     * @return iterable<mixed>
     */
    public function findBySpecification(Specification $specification): iterable;

    /**
     * Add an entity to the repository.
     *
     * @param mixed $entity The new entity.
     */
    public function add(mixed $entity): void;

    /**
     * Remove an entity from the repository.
     *
     * @param mixed $entity The entity.
     */
    public function remove(mixed $entity): void;
}
