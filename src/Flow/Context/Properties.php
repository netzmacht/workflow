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

namespace Netzmacht\Workflow\Flow\Context;

/**
 * Class Properties
 */
class Properties
{
    const NAMESPACE_DEFAULT = 'default';

    const NAMESPACE_ENTITY = 'entity';

    /**
     * Properties.
     *
     * @var array
     */
    private $properties = [];

    /**
     * Properties constructor.
     *
     * @param array $properties Properties.
     */
    public function __construct(array $properties = null)
    {
        $this->properties = $properties;
    }

    /**
     * Get properties.
     *
     * @param null|string $namespace Property namespace.
     *
     * @return array
     */
    public function getProperties(?string $namespace = null): array
    {
        if (!$namespace) {
            return $this->properties;
        }

        if (isset($this->properties[$namespace])) {
            return $this->properties[$namespace];
        }

        return array();
    }

    /**
     * Check if a property exists.
     *
     * @param string $propertyName Name of the property.
     * @param string $namespace    Property namespace.
     *
     * @return bool
     */
    public function hasProperty(string $propertyName, string $namespace = self::NAMESPACE_DEFAULT): bool
    {
        if (!array_key_exists($namespace, $this->properties)) {
            return false;
        }

        return array_key_exists($propertyName, $this->properties[self::NAMESPACE_DEFAULT]);
    }

    /**
     * Set a property value.
     *
     * @param string $propertyName Name of the property.
     * @param string $value        Value of the property.
     * @param string $namespace    Property namespace.
     *
     * @return Properties
     */
    public function setProperty(string $propertyName, $value, string $namespace = self::NAMESPACE_DEFAULT): self
    {
        $this->properties[$namespace][$propertyName] = $value;

        return $this;
    }
}
