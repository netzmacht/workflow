<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

/**
 * Class Configurable is the base class for each flow elements.
 */
abstract class Base
{
    /**
     * Configuration values.
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Name of the element.
     */
    private string $name;

    /**
     * Label of the element.
     */
    private string $label;

    /**
     * @param string               $name   Name of the element.
     * @param string               $label  Label of the element.
     * @param array<string, mixed> $config Configuration values.
     */
    public function __construct(string $name, string $label = '', array $config = [])
    {
        $this->name   = $name;
        $this->label  = $label ?: $name;
        $this->config = $config;
    }

    /**
     * Get element label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the label.
     *
     * @param string $label The label.
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get element name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set a config value.
     *
     * @param string $name  Config property name.
     * @param mixed  $value Config property value.
     *
     * @return $this
     */
    public function setConfigValue(string $name, mixed $value): self
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * Get a config value.
     *
     * @param string $name    Config property name.
     * @param mixed  $default Default value which is returned if config is not set.
     */
    public function getConfigValue(string $name, mixed $default = null): mixed
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        return $default;
    }

    /**
     * Consider if config value isset.
     *
     * @param string $name Name of the config value.
     */
    public function hasConfigValue(string $name): bool
    {
        return isset($this->config[$name]);
    }

    /**
     * Add multiple config properties.
     *
     * @param array<string, mixed> $values Config values.
     *
     * @return $this
     */
    public function addConfig(array $values): self
    {
        foreach ($values as $name => $value) {
            $this->setConfigValue($name, $value);
        }

        return $this;
    }

    /**
     * Remove a config property.
     *
     * @param string $name Config property name.
     *
     * @return $this
     */
    public function removeConfigValue(string $name): self
    {
        unset($this->config[$name]);

        return $this;
    }

    /**
     * Get configuration.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
