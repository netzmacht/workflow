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
    public function __construct(array $properties = [])
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
    public function toArray(?string $namespace = null): array
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
     *
     * @return bool
     */
    public function has(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->properties);
    }

    /**
     * Set a property value.
     *
     * @param string $propertyName Name of the property.
     * @param mixed  $value        Value of the property.
     *
     * @return Properties
     */
    public function set(string $propertyName, $value): self
    {
        $this->properties[$propertyName] = $value;

        return $this;
    }

    /**
     * Get the property value. If property does not exist, null is returned.
     *
     * @param string $propertyName Name of the property.
     *
     * @return mixed
     */
    public function get(string $propertyName)
    {
        if (isset($this->properties[$propertyName])) {
            return $this->properties[$propertyName];
        }

        return null;
    }
}
