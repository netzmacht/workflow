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

namespace Netzmacht\Workflow\Data;

use Assert\Assertion;

/**
 * Class EntityId identifies an entity by using its row id and provider name.
 *
 * @package Netzmacht\Workflow\Data
 */
final class EntityId
{
    /**
     * The identifier. Usually a database id.
     *
     * @var mixed
     */
    private $identifier;

    /**
     * The provider name. Usually the database table name.
     *
     * @var string
     */
    private $providerName;

    /**
     * Construct.
     *
     * @param string $providerName The provider name.
     * @param mixed  $identifier   The identifier.
     */
    private function __construct(string $providerName, $identifier)
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
        list($providerName, $identifier) = explode('::', $entityId, 2);

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
    public static function fromProviderNameAndId(string $providerName, $identifier): self
    {
        return new static($providerName, $identifier);
    }

    /**
     * Get the identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->providerName;
    }

    /**
     * Consider if it is equal with another entity id.
     *
     * @param EntityId $entityId The entity id to compare with.
     *
     * @return bool
     */
    public function equals(EntityId $entityId): bool
    {
        return ((string) $this == (string) $entityId);
    }

    /**
     * Cast entity id to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->providerName . '::' . $this->identifier;
    }
}
