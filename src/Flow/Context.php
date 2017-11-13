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

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;

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
     * @var Properties
     */
    private $properties;

    /**
     * Transition payload.
     *
     * @var Properties
     */
    private $payload;

    /**
     * Error collection.
     *
     * @var ErrorCollection
     */
    private $errorCollection;

    /**
     * Construct.
     *
     * @param Properties           $properties      The properties to be stored.
     * @param Properties           $payload         The given parameters.
     * @param ErrorCollection|null $errorCollection Error collection.
     */
    public function __construct(
        Properties $properties = null,
        Properties $payload = null,
        ErrorCollection $errorCollection = null
    ) {
        $this->properties      = $properties ?: new Properties();
        $this->payload         = $payload ?: new Properties();
        $this->errorCollection = $errorCollection ?: new ErrorCollection();
    }

    /**
     * Get properties.
     *
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get payload.
     *
     * @return Properties
     */
    public function getPayload()
    {
        return $this->payload;
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
