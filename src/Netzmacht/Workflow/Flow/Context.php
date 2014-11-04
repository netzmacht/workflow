<?php

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\ErrorCollection;

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
     * Errors being raised during transition.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Params being passed.
     *
     * @var array
     */
    private $params;


    /**
     * Construct.
     *
     * @param array $properties The properties to be stored.
     * @param array $params     The given parameters.
     */
    public function __construct(array $properties = array(), array $params = array())
    {
        $this->properties      = $properties;
        $this->params          = $params;
        $this->errorCollection = new ErrorCollection();
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
     * @param string $name Property name.
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
     * @param string $name Property name.
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
     * Consider if an error isset.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->errorCollection->hasErrors();
    }

    /**
     * Add a new error message.
     *
     * @param string $message The message.
     * @param array  $params  Message parameters.
     *
     * @return $this
     */
    public function addError($message, array $params = array())
    {
        $this->errorCollection->addError($message, $params);

        return $this;
    }

    /**
     * Get the error collection.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection()
    {
        return $this->errorCollection;
    }

    /**
     * Set a parameter.
     *
     * @param string $name  Param name.
     * @param mixed  $value Param value.
     *
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * Consider if a param isset.
     *
     * @param string $name Param name.
     *
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * Get all params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a param by name.
     *
     * @param string $name Param name.
     *
     * @return mixed
     */
    public function getParam($name)
    {
        if ($this->hasParam($name)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Set multiple params.
     *
     * @param array $params Array of params.
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
