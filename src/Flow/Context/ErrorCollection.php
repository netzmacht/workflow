<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Context;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Override;
use ReturnTypeWillChange;

use function array_map;
use function count;

/**
 * Class ErrorCollection collects error messages being raised during transition.
 *
 * @implements IteratorAggregate<int, TError>
 * @psalm-type TError = array{0: string, 1: list<string>|array<string, string>, 2: ErrorCollection|null}
 * @psalm-type TErrorArray = list<array{0: string, 1: list<string>|array<string, string>, 2: list<array>|null}>
 */
final class ErrorCollection implements IteratorAggregate, Countable
{
    /**
     * Stored errors.
     *
     * @var list<TError>
     */
    private array $errors = [];

    /**
     * Construct.
     *
     * @param list<TError> $errors Initial error messages.
     */
    public function __construct(array $errors = [])
    {
        $this->addErrors($errors);
    }

    /**
     * Add a new error.
     *
     * @param string                             $message    Error message.
     * @param list<string>|array<string, string> $params     Params for the error message.
     * @param ErrorCollection|null               $collection Option. Child collection of the error.
     *
     * @return $this
     */
    public function addError(string $message, array $params = [], ErrorCollection|null $collection = null)
    {
        $this->errors[] = [$message, $params, $collection];

        return $this;
    }

    /**
     * Check if any error isset.
     */
    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * Count error messages.
     */
    public function countErrors(): int
    {
        return count($this->errors);
    }

    /**
     * Get an error by its index.
     *
     * @param int $index Error index.
     *
     * @return TError
     *
     * @throws InvalidArgumentException If the error index is not set.
     */
    public function getError(int $index): array
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }

        throw new InvalidArgumentException('Error with index "' . $index . '" not set.');
    }

    /**
     * Reset error collection.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->errors = [];

        return $this;
    }

    /**
     * Add a set of errors.
     *
     * @param list<TError> $errors List of errors.
     *
     * @return $this
     */
    public function addErrors(array $errors): self
    {
        foreach ($errors as $error) {
            [$message, $params, $collection] = $error;

            $this->addError($message, $params, $collection);
        }

        return $this;
    }

    /**
     * Get all errors.
     *
     * @return list<TError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    #[ReturnTypeWillChange]
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->errors);
    }

    #[Override]
    public function count(): int
    {
        return $this->countErrors();
    }

    /**
     * Convert the error collection to an array.
     *
     * @return TErrorArray
     */
    public function toArray(): array
    {
        return array_map(
            static function ($error) {
                $error[2] = $error[2]?->toArray();

                return $error;
            },
            $this->errors,
        );
    }
}
