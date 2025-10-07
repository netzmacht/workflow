<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Data;

use Assert\Assertion;

use function explode;
use function is_numeric;

/**
 * Class EntityId identifies an entity by using its row id and provider name.
 */
final class EntityId
{
    /**
     * The identifier. Usually a database id.
     */
    private mixed $identifier;

    /**
     * The provider name. Usually the database table name.
     */
    private string $providerName;

    /**
     * Construct.
     *
     * @param string $providerName The provider name.
     * @param mixed  $identifier   The identifier.
     */
    private function __construct(string $providerName, mixed $identifier)
    {
        // cast to int, but not for uuids
        if (is_numeric($identifier)) {
            $identifier = (int) $identifier;
        }

        $this->providerName = $providerName;
        $this->identifier   = $identifier;
    }

    /**
     * Great the entity id from an string.
     *
     * @param string $entityId Entity id as string representation. For example provider::2.
     *
     * @return static
     */
    public static function fromString(string $entityId): self
    {
        [$providerName, $identifier] = explode('::', $entityId, 2);

        Assertion::notEmpty($providerName);
        Assertion::notEmpty($identifier);

        return new static($providerName, $identifier);
    }

    /**
     * Create the entity id by provider name and identifier.
     *
     * @param string $providerName The provider name.
     * @param mixed  $identifier   The identifier.
     *
     * @return static
     */
    public static function fromProviderNameAndId(string $providerName, mixed $identifier): self
    {
        return new static($providerName, $identifier);
    }

    /**
     * Get the identifier.
     */
    public function getIdentifier(): mixed
    {
        return $this->identifier;
    }

    /**
     * Get the provider name.
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Consider if it is equal with another entity id.
     *
     * @param EntityId $entityId The entity id to compare with.
     */
    public function equals(EntityId $entityId): bool
    {
        return (string) $this === (string) $entityId;
    }

    /**
     * Cast entity id to string.
     */
    public function __toString(): string
    {
        return $this->providerName . '::' . $this->identifier;
    }
}
