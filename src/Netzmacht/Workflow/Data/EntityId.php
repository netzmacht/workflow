<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Data;

/**
 * Class EntityId identifies an entity by using its row id and provider name.
 *
 * @package Netzmacht\Workflow\Data
 */
class EntityId
{
    /**
     * The identifier. Usually a database id.
     *
     * @var int
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
     * @param int    $identifier   The identifier.
     */
    private function __construct($providerName, $identifier)
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
     * @param $entityId Entity id as string representation. For example provider::2.
     *
     * @return static
     */
    public static function fromString($entityId)
    {
        list($providerName, $identifier) = explode('::', $entityId, 2);

        return new static($providerName, $identifier);
    }

    /**
     * Create the entity id by provider name and identifier.
     *
     * @param string $providerName The provider name.
     * @param int    $identifier   The identifier.
     *
     * @return static
     */
    public static function fromProviderNameAndId($providerName, $identifier)
    {
        return new static($providerName, $identifier);
    }

    /**
     * Get the identifier.
     *
     * @return int
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
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Consider if it is equal with another entity id.
     *
     * @param EntityId $entityId
     *
     * @return bool
     */
    public function equals(EntityId $entityId)
    {
        return ((string) $this == (string) $entityId);
    }

    /**
     * Cast entity id to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->providerName . '::' . $this->identifier;
    }
}
