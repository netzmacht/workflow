<?php

namespace spec\Netzmacht\Workflow\Util;

use Netzmacht\Workflow\Util\Comparison;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ComparisonSpec
 * @package spec\Netzmacht\Workflow\Util
 * @mixin Comparison
 */
class ComparisonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Util\Comparison');
    }

    function it_checks_if_values_equals()
    {
        $this->equals(1, 2)->shouldReturn(false);
        $this->equals(1, 1)->shouldReturn(true);
    }

    function it_checks_if_values_not_equals()
    {
        $this->notEquals(1, 2)->shouldReturn(true);
        $this->notEquals(1, 1)->shouldReturn(false);
    }

    function it_checks_if_values_are_identical()
    {
        $this->identical(1, '1')->shouldReturn(false);
        $this->identical(1, 1)->shouldReturn(true);
    }

    function it_checks_if_values_are_not_identical()
    {
        $this->notIdentical(1, '1')->shouldReturn(true);
        $this->notIdentical(1, 1)->shouldReturn(false);
    }

    function it_checks_if_value_is_greater_than_other()
    {
        $this->greaterThan(1, 2)->shouldReturn(false);
        $this->greaterThan(1, 1)->shouldReturn(false);
        $this->greaterThan(2, 1)->shouldReturn(true);
    }

    function it_checks_if_value_is_greater_than_or_equals_other()
    {
        $this->greaterThanOrEquals(1, 2)->shouldReturn(false);
        $this->greaterThanOrEquals(1, 1)->shouldReturn(true);
        $this->greaterThanOrEquals(2, 1)->shouldReturn(true);
    }

    function it_checks_if_value_is_lesser_than_other()
    {
        $this->lesserThan(2, 1)->shouldReturn(false);
        $this->lesserThan(1, 1)->shouldReturn(false);
        $this->lesserThan(1, 2)->shouldReturn(true);
    }

    function it_checks_if_value_is_lesser_than_or_equals_other()
    {
        $this->lesserThanOrEquals(2, 1)->shouldReturn(false);
        $this->lesserThanOrEquals(1, 1)->shouldReturn(true);
        $this->lesserThanOrEquals(1, 2)->shouldReturn(true);
    }

    function it_compare_handles_equals()
    {
        $this->compare(1, 2, Comparison::EQUALS)->shouldReturn(false);
        $this->compare(2, 1, Comparison::EQUALS)->shouldReturn(false);
        $this->compare(1, 1, Comparison::EQUALS)->shouldReturn(true);
    }

    function it_compare_handles_not_equals()
    {
        $this->compare(1, 2, Comparison::NOT_EQUALS)->shouldReturn(true);
        $this->compare(2, 1, Comparison::NOT_EQUALS)->shouldReturn(true);
        $this->compare(1, 1, Comparison::NOT_EQUALS)->shouldReturn(false);
    }

    function it_compare_handles_identical()
    {
        $this->compare(1, '1', Comparison::IDENTICAL)->shouldReturn(false);
        $this->compare(1, 1, Comparison::IDENTICAL)->shouldReturn(true);
    }

    function it_compare_handles_not_identical()
    {
        $this->compare(1, '1', Comparison::NOT_IDENTICAL)->shouldReturn(true);
        $this->compare(1, 1, Comparison::NOT_IDENTICAL)->shouldReturn(false);
    }

    function it_compare_handles_greater_than()
    {
        $this->compare(1, 2, Comparison::GREATER_THAN)->shouldReturn(false);
        $this->compare(2, 2, Comparison::GREATER_THAN)->shouldReturn(false);
        $this->compare(2, 1, Comparison::GREATER_THAN)->shouldReturn(true);
    }

    function it_compare_handles_greater_than_or_equals()
    {
        $this->compare(1, 2, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(false);
        $this->compare(2, 2, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(true);
        $this->compare(2, 1, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(true);
    }

    function it_compare_handles_lesser_than()
    {
        $this->compare(1, 2, Comparison::LESSER_THAN)->shouldReturn(true);
        $this->compare(2, 2, Comparison::LESSER_THAN)->shouldReturn(false);
        $this->compare(2, 1, Comparison::LESSER_THAN)->shouldReturn(false);
    }

    function it_compare_handles_lesser_than_or_equals()
    {
        $this->compare(2, 1, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(false);
        $this->compare(2, 2, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(true);
        $this->compare(1, 2, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(true);
    }
}
