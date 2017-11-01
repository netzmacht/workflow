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

namespace Netzmacht\Workflow;

/**
 * Class Configurable is the base class for each flow elements.
 *
 * @package Netzmacht\Workflow\Flow
 */
abstract class Base
{
    /**
     * Configuration values.
     *
     * @var array
     */
    private $config = array();

    /**
     * Name of the element.
     *
     * @var string
     */
    private $name;

    /**
     * Label of the element.
     *
     * @var string
     */
    private $label;

    /**
     * Construct.
     *
     * @param string $name   Name of the element.
     * @param string $label  Label of the element.
     * @param array  $config Configuration values.
     */
    public function __construct(string $name, string $label = '', array $config = array())
    {
        $this->name   = $name;
        $this->label  = $label ?: $name;
        $this->config = $config;
    }

    /**
     * Get element label.
     *
     * @return string
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
     *
     * @return string
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
    public function setConfigValue(string $name, $value): self
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * Get a config value.
     *
     * @param string $name    Config property name.
     * @param mixed  $default Default value which is returned if config is not set.
     *
     * @return mixed
     */
    public function getConfigValue(string $name, $default = null)
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
     *
     * @return bool
     */
    public function hasConfigValue(string $name): bool
    {
        return isset($this->config[$name]);
    }

    /**
     * Add multiple config properties.
     *
     * @param array $values Config values.
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
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
