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
     * Transition payload.
     *
     * @var array
     */
    private $payload = array();

    /**
     * Error collection.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Construct.
     *
     * @param array                $properties      The properties to be stored.
     * @param array                $payload         The given parameters.
     * @param ErrorCollection|null $errorCollection Error collection.
     */
    public function __construct(array $properties = [], array $payload = [], ErrorCollection $errorCollection = null)
    {
        $this->properties      = $properties;
        $this->payload         = $payload;
        $this->errorCollection = $errorCollection ?: new ErrorCollection();
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
    public function setProperty(string $name, $value, string $namespace = self::NAMESPACE_DEFAULT): self
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
    public function getProperty(string $name, string $namespace = self::NAMESPACE_DEFAULT)
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
    public function hasProperty(string $name, string $namespace = self::NAMESPACE_DEFAULT): bool
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
     * Set a parameter.
     *
     * @param string $name      Param name.
     * @param mixed  $value     Param value.
     * @param string $namespace Namespace the param belongs to.
     *
     * @return $this
     */
    public function setParam(string $name, $value, string $namespace = self::NAMESPACE_DEFAULT): self
    {
        $this->payload[$namespace][$name] = $value;

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
    public function hasParam(string $name, string $namespace = self::NAMESPACE_DEFAULT): bool
    {
        return isset($this->payload[$namespace][$name]);
    }

    /**
     * Get all params.
     *
     * If namespace is given only a specific namespace is returned. Otherwise all namespaces are returned.
     *
     * @param string|null $namespace Optional namespace.
     *
     * @return array
     */
    public function getPayload(?string $namespace = null): array
    {
        if ($namespace) {
            if (isset($this->payload[$namespace])) {
                return $this->payload[$namespace];
            }

            return array();
        }

        return $this->payload;
    }

    /**
     * Get a param by name.
     *
     * @param string $name      Param name.
     * @param string $namespace Namespace the param belongs to.
     *
     * @return mixed
     */
    public function getParam(string $name, ?string $namespace = self::NAMESPACE_DEFAULT)
    {
        if ($this->hasParam($name, $namespace)) {
            return $this->payload[$namespace][$name];
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
    public function setPayload(array $params, ?string $namespace = null): self
    {
        if ($namespace) {
            $this->payload[$namespace] = $params;
        } else {
            $this->payload = $params;
        }

        return $this;
    }

    /**
     * Get error collection.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection(): ErrorCollection
    {
        return $this->errorCollection;
    }

    /**
     * Add an error.
     *
     * @param string          $message    Error message.
     * @param array           $params     Params for the error message.
     * @param ErrorCollection $collection Option. Child collection of the error.
     *
     * @return self
     */
    public function addError(string $message, array $params = array(), ErrorCollection $collection = null): self
    {
        $this->errorCollection->addError($message, $params, $collection);

        return $this;
    }

    /**
     * Get a new context with an empty error collection.
     *
     * @return Context
     */
    public function withEmptyErrorCollection(): Context
    {
        return new Context($this->properties, $this->payload, new ErrorCollection());
    }
}
