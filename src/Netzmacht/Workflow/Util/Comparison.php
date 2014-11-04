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
        switch ($operator) {
            case static::EQUALS:
                return static::equals($valueA, $valueB);

            case static::NOT_EQUALS:
                return static::notEquals($valueA, $valueB);

            case static::IDENTICAL:
                return static::identical($valueA, $valueB);

            case static::NOT_IDENTICAL:
                return static::notIdentical($valueA, $valueB);

            case static::GREATER_THAN:
                return static::greaterThan($valueA, $valueB);

            case static::GREATER_THAN_OR_EQUALS:
                return static::greaterThanOrEquals($valueA, $valueB);

            case static::LESSER_THAN:
                return static::lesserThan($valueA, $valueB);

            case static::LESSER_THAN_OR_EQUALS:
                return static::lesserThanOrEquals($valueA, $valueB);

            default:
                return false;
        }
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
}
