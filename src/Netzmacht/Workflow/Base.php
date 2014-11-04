<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

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
     * Identifier of database model.
     *
     * @var int
     */
    private $modelId;

    /**
     * Construct.
     *
     * @param string $name    Name of the element.
     * @param string $label   Label of the element.
     * @param array  $config  Configuration values.
     * @param int    $modelId Optional database id.
     */
    public function __construct($name, $label = null, array $config = array(), $modelId = null)
    {
        $this->name    = $name;
        $this->label   = $label ?: $name;
        $this->config  = $config;
        $this->modelId = $modelId;
    }

    /**
     * Get element label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label.
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get element name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * Set a config value.
     *
     * @param string $name  Config property name.
     * @param mixed  $value Config property value.
     *
     * @return $this
     */
    public function setConfigValue($name, $value)
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
     * @return null
     */
    public function getConfigValue($name, $default=null)
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
    public function hasConfigValue($name)
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
    public function addConfig(array $values)
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
    public function removeConfigValue($name)
    {
        unset($this->config[$name]);

        return $this;
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
