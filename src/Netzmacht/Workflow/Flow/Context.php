<?php

namespace Netzmacht\Workflow\Flow;

/**
 * Class Context provides extra information for a transition.
 *
 * @package Netzmacht\Workflow\Flow
 */
class Context
{
    const NAMESPACE_DEFAULT = 'default';

    const NAMESPACE_ENTITY = 'entity';

    /**
     * Properties which will be stored as state data.
     *
     * @var array
     */
    private $properties = array();

    /**
     * Params being passed.
     *
     * @var array
     */
    private $params = array();


    /**
     * Construct.
     *
     * @param array $properties The properties to be stored.
     * @param array $params     The given parameters.
     */
    public function __construct(array $properties = array(), array $params = array())
    {
        $this->properties = $properties;
        $this->params     = $params;
    }

    /**
     * Set a property value.
     *
     * @param string $name      Name of the property.
     * @param mixed  $value     Value of the property.
     * @param string $namespace Namespace the property belongs to.
     *
     * @return $this
     */
    public function setProperty($name, $value, $namespace = self::NAMESPACE_DEFAULT)
    {
        $this->properties[$namespace][$name] = $value;

        return $this;
    }

    /**
     * Get the property value.
     *
     * @param string $name      Property name.
     * @param string $namespace Namespace the property belongs to.
     *
     * @return mixed
     */
    public function getProperty($name, $namespace = self::NAMESPACE_DEFAULT)
    {
        if ($this->hasProperty($name, $namespace)) {
            return $this->properties[$namespace][$name];
        }

        return null;
    }

    /**
     * Consider if property is set.
     *
     * @param string $name      Property name.
     * @param string $namespace Namespace the property belongs to.
     *
     * @return bool
     */
    public function hasProperty($name, $namespace = self::NAMESPACE_DEFAULT)
    {
        return isset($this->properties[$namespace][$name]);
    }

    /**
     * Get all properties. If namespace isset only properties of a namespace.
     *
     * @param string $namespace Namespace the property belongs to.
     *
     * @return array
     */
    public function getProperties($namespace = null)
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
     * Set a parameter.
     *
     * @param string $name      Param name.
     * @param mixed  $value     Param value.
     * @param string $namespace Namespace the param belongs to.
     *
     * @return $this
     */
    public function setParam($name, $value, $namespace = self::NAMESPACE_DEFAULT)
    {
        $this->params[$namespace][$name] = $value;

        return $this;
    }

    /**
     * Consider if a param isset.
     *
     * @param string $name      Param name.
     * @param string $namespace Namespace the param belongs to.
     *
     * @return bool
     */
    public function hasParam($name, $namespace = self::NAMESPACE_DEFAULT)
    {
        return isset($this->params[$namespace][$name]);
    }

    /**
     * Get all params.
     *
     * If namespace is given only a specific namespace is returned. Otherwise all namesapces are returned.
     *
     * @param string|null $namespace Optional namespace.
     *
     * @return array
     */
    public function getParams($namespace = null)
    {
        if ($namespace) {
            if (isset($this->params[$namespace])) {
                return $this->params[$namespace];
            }

            return array();
        }

        return $this->params;
    }

    /**
     * Get a param by name.
     *
     * @param string $name      Param name.
     * @param string $namespace Namespace the param belongs to.
     *
     * @return mixed
     */
    public function getParam($name, $namespace = self::NAMESPACE_DEFAULT)
    {
        if ($this->hasParam($name, $namespace)) {
            return $this->params[$namespace][$name];
        }

        return null;
    }

    /**
     * Set multiple params.
     *
     * @param array  $params    Array of params.
     * @param string $namespace Namespace the params belongs to.
     *
     * @return $this
     */
    public function setParams(array $params, $namespace = null)
    {
        if ($namespace) {
            $this->params[$namespace] = $params;
        } else {
            $this->params = $params;
        }

        return $this;
    }
}
