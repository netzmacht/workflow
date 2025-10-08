<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Util;

use ReflectionClass;

use function array_map;
use function call_user_func;
use function explode;
use function implode;
use function lcfirst;
use function strtolower;
use function ucfirst;

/**
 * Class Comparison is a util class to allow value comparison by passing two values.
 */
final class Comparison
{
    public const string EQUALS                 = '==';
    public const string IDENTICAL              = '===';
    public const string NOT_EQUALS             = '!=';
    public const string NOT_IDENTICAL          = '!==';
    public const string GREATER_THAN           = '>';
    public const string LESSER_THAN            = '<';
    public const string LESSER_THAN_OR_EQUALS  = '<=';
    public const string GREATER_THAN_OR_EQUALS = '>=';

    /**
     * Operation method mapping cache.
     *
     * @var array<string, string>
     */
    private static array $operators;

    /**
     * Compare two values.
     *
     * @param mixed  $valueA   Value a.
     * @param mixed  $valueB   Value b.
     * @param string $operator The operator for the comparison.
     */
    public static function compare(mixed $valueA, mixed $valueB, string $operator): bool
    {
        $method = self::getOperatorMethod($operator);

        if ($method !== null) {
            return call_user_func([self::class, $method], $valueA, $valueB);
        }

        return false;
    }

    /**
     * Consider if two values equal.
     */
    public static function equals(mixed $valueA, mixed $valueB): bool
    {
        return $valueA === $valueB;
    }

    /**
     * Consider if both values are identical.
     *
     * It uses the === operator of php.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function identical(mixed $valueA, mixed $valueB): bool
    {
        return $valueA === $valueB;
    }

    /**
     * Consider if two values not equals.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function notEquals(mixed $valueA, mixed $valueB): bool
    {
        return ! self::equals($valueA, $valueB);
    }

    /**
     * Consider if two values are not identical.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function notIdentical(mixed $valueA, mixed $valueB): bool
    {
        return ! self::identical($valueA, $valueB);
    }

    /**
     * Consider if value a is greater than value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function greaterThan(mixed $valueA, mixed $valueB): bool
    {
        return $valueA > $valueB;
    }

    /**
     * Consider if value a is greater than or equals value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function greaterThanOrEquals(mixed $valueA, mixed $valueB): bool
    {
        return $valueA >= $valueB;
    }

    /**
     * Consider if value a is lesser than value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function lesserThan(mixed $valueA, mixed $valueB): bool
    {
        return $valueA < $valueB;
    }

    /**
     * Consider if value a is lesser than or equals value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     */
    public static function lesserThanOrEquals(mixed $valueA, mixed $valueB): bool
    {
        return $valueA <= $valueB;
    }

    /**
     * Get operator method. Returns false if metod not set.
     *
     * @param string $operator The current operator.
     */
    private static function getOperatorMethod(string $operator): string|null
    {
        return self::getOperators()[$operator] ?? null;
    }

    /**
     * Get operator method mapping.
     *
     * @return array<string, string>
     */
    private static function getOperators(): array
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (! isset(self::$operators)) {
            $reflector = new ReflectionClass(self::class);
            $constants = $reflector->getConstants();
            $operators = [];

            foreach ($constants as $name => $operator) {
                $parts = explode('_', $name);
                $parts = array_map(
                    static function ($item) {
                        $item = strtolower($item);
                        $item = ucfirst($item);

                        return $item;
                    },
                    $parts,
                );

                $operators[$operator] = lcfirst(implode('', $parts));
            }

            self::$operators = $operators;
        }

        return self::$operators;
    }
}
