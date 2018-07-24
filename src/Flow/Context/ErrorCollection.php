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
 * Class ErrorCollection collects error messages being raised during transition.
 *
 * @package Netzmacht\Workflow
 */
class ErrorCollection implements \IteratorAggregate
{
    /**
     * Stored errors.
     *
     * @var array
     */
    private $errors = array();

    /**
     * Construct.
     *
     * @param array $errors Initial error messages.
     */
    public function __construct(array $errors = array())
    {
        $this->addErrors($errors);
    }

    /**
     * Add a new error.
     *
     * @param string          $message    Error message.
     * @param array           $params     Params for the error message.
     * @param ErrorCollection $collection Option. Child collection of the error.
     *
     * @return $this
     */
    public function addError(string $message, array $params = array(), ErrorCollection $collection = null)
    {
        $this->errors[] = array($message, $params, $collection);

        return $this;
    }

    /**
     * Check if any error isset.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Count error messages.
     *
     * @return int
     */
    public function countErrors(): int
    {
        return count($this->errors);
    }

    /**
     * Get an error by it's index.
     *
     * @param int $index Error index.
     *
     * @throws \InvalidArgumentException If error index is not set.
     *
     * @return array
     */
    public function getError(int $index): array
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }

        throw new \InvalidArgumentException('Error with index "' . $index . '" not set.');
    }

    /**
     * Reset error collection.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->errors = array();

        return $this;
    }

    /**
     * Add a set of errors.
     *
     * @param array $errors List of errors.
     *
     * @return $this
     */
    public function addErrors(array $errors): self
    {
        foreach ($errors as $error) {
            list($message, $params, $collection) = (array) $error;

            $this->addError($message, $params, $collection);
        }

        return $this;
    }

    /**
     * Get all errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * Convert error collection to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(
            function ($error) {
                if ($error[2]) {
                    /** @var ErrorCollection $collection */
                    $collection = $error[2];
                    $error[2]   = $collection->toArray();
                }

                return $error;
            },
            $this->errors
        );
    }
}
