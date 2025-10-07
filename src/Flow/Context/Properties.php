<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Context;

use function array_key_exists;

class Properties
{
    /** @param array<string, mixed> $properties Properties. */
    public function __construct(private array $properties = [])
    {
    }

    /**
     * Get properties.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->properties;
    }

    /**
     * Check if a property exists.
     *
     * @param string $propertyName Name of the property.
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
    public function set(string $propertyName, mixed $value): self
    {
        $this->properties[$propertyName] = $value;

        return $this;
    }

    /**
     * Get the property value. If property does not exist, null is returned.
     *
     * @param string $propertyName Name of the property.
     */
    public function get(string $propertyName): mixed
    {
        if (isset($this->properties[$propertyName])) {
            return $this->properties[$propertyName];
        }

        return null;
    }
}
