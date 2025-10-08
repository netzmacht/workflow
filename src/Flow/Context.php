<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;

/**
 * Class Context provides extra information for a transition.
 *
 * @psalm-suppress ClassMustBeFinal
 */
class Context
{
    public const string NAMESPACE_DEFAULT = 'default';

    public const string NAMESPACE_ENTITY = 'entity';

    /**
     * Properties which will be stored as state data.
     */
    private Properties $properties;

    /**
     * Transition payload.
     */
    private Properties $payload;

    /**
     * Error collection.
     */
    private ErrorCollection $errorCollection;

    /**
     * Construct.
     *
     * @param Properties|null      $properties      The properties to be stored.
     * @param Properties|null      $payload         The given parameters.
     * @param ErrorCollection|null $errorCollection Error collection.
     */
    public function __construct(
        Properties|null $properties = null,
        Properties|null $payload = null,
        ErrorCollection|null $errorCollection = null,
    ) {
        $this->properties      = $properties ?? new Properties();
        $this->payload         = $payload ?? new Properties();
        $this->errorCollection = $errorCollection ?? new ErrorCollection();
    }

    /**
     * Get properties.
     */
    public function getProperties(): Properties
    {
        return $this->properties;
    }

    /**
     * Get payload.
     */
    public function getPayload(): Properties
    {
        return $this->payload;
    }

    /**
     * Get error collection.
     */
    public function getErrorCollection(): ErrorCollection
    {
        return $this->errorCollection;
    }

    /**
     * Add an error.
     *
     * @param string                             $message    Error message.
     * @param list<string>|array<string, string> $params     Params for the error message.
     * @param ErrorCollection|null               $collection Option. Child collection of the error.
     */
    public function addError(string $message, array $params = [], ErrorCollection|null $collection = null): self
    {
        $this->errorCollection->addError($message, $params, $collection);

        return $this;
    }

    /**
     * Get a new context with an empty error collection.
     *
     * @param array<string, mixed>|null $payload Optional pass a new set of payload.
     */
    public function createCleanCopy(array|null $payload = null): Context
    {
        if ($payload !== null) {
            $payload = new Properties($payload);
        } else {
            $payload = $this->payload;
        }

        return new Context($this->properties, $payload, new ErrorCollection());
    }
}
