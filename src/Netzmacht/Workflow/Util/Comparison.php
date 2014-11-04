<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Util;

/**
 * Class Comparison is an util class to allow value comparison by passing two values.
 *
 * @package Netzmacht\Workflow\Util
 */
class Comparison
{
    const EQUALS                 = '==';
    const IDENTICAL              = '===';
    const NOT_EQUALS             = '!=';
    const NOT_IDENTICAL          = '!==';
    const GREATER_THAN           = '>';
    const LESSER_THAN            = '<';
    const LESSER_THAN_OR_EQUALS  = '<=';
    const GREATER_THAN_OR_EQUALS = '>=';

    /**
     * Operation method mapping cache.
     *
     * @var array
     */
    private static $operators;

    /**
     * Compare two values.
     *
     * @param mixed  $valueA   Value a.
     * @param mixed  $valueB   Value b.
     * @param string $operator The operator vor the comparison.
     *
     * @return bool
     */
    public static function compare($valueA, $valueB, $operator)
    {
        $method = self::getOperatorMethod($operator);

        if ($method) {
            return call_user_func(array(get_called_class(), $method), $valueA, $valueB);
        }

        return false;

    }

    /**
     * Consider if two values equals.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function equals($valueA, $valueB)
    {
        return $valueA == $valueB;
    }

    /**
     * Consider if both values are identical.
     *
     * It uses the === operator of php.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function identical($valueA, $valueB)
    {
        return $valueA === $valueB;
    }

    /**
     * Consider if two values not equals.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function notEquals($valueA, $valueB)
    {
        return !static::equals($valueA, $valueB);
    }

    /**
     * Consider if two values are not identical.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function notIdentical($valueA, $valueB)
    {
        return !static::identical($valueA, $valueB);
    }

    /**
     * Consider if value a is greater than value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function greaterThan($valueA, $valueB)
    {
        return $valueA > $valueB;
    }

    /**
     * Consider if value a is greater than or equals value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function greaterThanOrEquals($valueA, $valueB)
    {
        return $valueA >= $valueB;
    }

    /**
     * Consider if value a is lesser than value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function lesserThan($valueA, $valueB)
    {
        return $valueA < $valueB;
    }

    /**
     * Consider if value a is lesser than or equals value b.
     *
     * @param mixed $valueA Value a.
     * @param mixed $valueB Value b.
     *
     * @return bool
     */
    public static function lesserThanOrEquals($valueA, $valueB)
    {
        return $valueA <= $valueB;
    }

    /**
     * Get operator method. Returns false if metod not set.
     *
     * @param string $operator The current operator.
     *
     * @return string|bool
     */
    private static function getOperatorMethod($operator)
    {
        $operators = self::getOperators();

        if (isset($operators[$operator])) {
            return $operators[$operator];
        }

        return false;
    }

    /**
     * Get operator method mapping.
     *
     * @return array
     */
    private static function getOperators()
    {
        if (!is_array(self::$operators)) {
            $reflector = new \ReflectionClass(get_called_class());
            $constants = $reflector->getConstants();
            $operators = array();

            foreach ($constants as $name => $operator) {
                $parts = explode('_', $name);
                $parts = array_map(
                    function ($item) {
                        $item = strtolower($item);
                        $item = ucfirst($item);

                        return $item;
                    },
                    $parts
                );

                $operators[$operator] = lcfirst(implode('', $parts));
            }

            self::$operators = $operators;
        }

        return self::$operators;
    }
}
