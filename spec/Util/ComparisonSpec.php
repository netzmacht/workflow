<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Util;

use Netzmacht\Workflow\Util\Comparison;
use PhpSpec\ObjectBehavior;

class ComparisonSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Util\Comparison');
    }

    public function it_checks_if_values_equals(): void
    {
        $this->equals(1, 2)->shouldReturn(false);
        $this->equals(1, 1)->shouldReturn(true);
    }

    public function it_checks_if_values_not_equals(): void
    {
        $this->notEquals(1, 2)->shouldReturn(true);
        $this->notEquals(1, 1)->shouldReturn(false);
    }

    public function it_checks_if_values_are_identical(): void
    {
        $this->identical(1, '1')->shouldReturn(false);
        $this->identical(1, 1)->shouldReturn(true);
    }

    public function it_checks_if_values_are_not_identical(): void
    {
        $this->notIdentical(1, '1')->shouldReturn(true);
        $this->notIdentical(1, 1)->shouldReturn(false);
    }

    public function it_checks_if_value_is_greater_than_other(): void
    {
        $this->greaterThan(1, 2)->shouldReturn(false);
        $this->greaterThan(1, 1)->shouldReturn(false);
        $this->greaterThan(2, 1)->shouldReturn(true);
    }

    public function it_checks_if_value_is_greater_than_or_equals_other(): void
    {
        $this->greaterThanOrEquals(1, 2)->shouldReturn(false);
        $this->greaterThanOrEquals(1, 1)->shouldReturn(true);
        $this->greaterThanOrEquals(2, 1)->shouldReturn(true);
    }

    public function it_checks_if_value_is_lesser_than_other(): void
    {
        $this->lesserThan(2, 1)->shouldReturn(false);
        $this->lesserThan(1, 1)->shouldReturn(false);
        $this->lesserThan(1, 2)->shouldReturn(true);
    }

    public function it_checks_if_value_is_lesser_than_or_equals_other(): void
    {
        $this->lesserThanOrEquals(2, 1)->shouldReturn(false);
        $this->lesserThanOrEquals(1, 1)->shouldReturn(true);
        $this->lesserThanOrEquals(1, 2)->shouldReturn(true);
    }

    public function it_compare_handles_equals(): void
    {
        $this->compare(1, 2, Comparison::EQUALS)->shouldReturn(false);
        $this->compare(2, 1, Comparison::EQUALS)->shouldReturn(false);
        $this->compare(1, 1, Comparison::EQUALS)->shouldReturn(true);
    }

    public function it_compare_handles_not_equals(): void
    {
        $this->compare(1, 2, Comparison::NOT_EQUALS)->shouldReturn(true);
        $this->compare(2, 1, Comparison::NOT_EQUALS)->shouldReturn(true);
        $this->compare(1, 1, Comparison::NOT_EQUALS)->shouldReturn(false);
    }

    public function it_compare_handles_identical(): void
    {
        $this->compare(1, '1', Comparison::IDENTICAL)->shouldReturn(false);
        $this->compare(1, 1, Comparison::IDENTICAL)->shouldReturn(true);
    }

    public function it_compare_handles_not_identical(): void
    {
        $this->compare(1, '1', Comparison::NOT_IDENTICAL)->shouldReturn(true);
        $this->compare(1, 1, Comparison::NOT_IDENTICAL)->shouldReturn(false);
    }

    public function it_compare_handles_greater_than(): void
    {
        $this->compare(1, 2, Comparison::GREATER_THAN)->shouldReturn(false);
        $this->compare(2, 2, Comparison::GREATER_THAN)->shouldReturn(false);
        $this->compare(2, 1, Comparison::GREATER_THAN)->shouldReturn(true);
    }

    public function it_compare_handles_greater_than_or_equals(): void
    {
        $this->compare(1, 2, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(false);
        $this->compare(2, 2, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(true);
        $this->compare(2, 1, Comparison::GREATER_THAN_OR_EQUALS)->shouldReturn(true);
    }

    public function it_compare_handles_lesser_than(): void
    {
        $this->compare(1, 2, Comparison::LESSER_THAN)->shouldReturn(true);
        $this->compare(2, 2, Comparison::LESSER_THAN)->shouldReturn(false);
        $this->compare(2, 1, Comparison::LESSER_THAN)->shouldReturn(false);
    }

    public function it_compare_handles_lesser_than_or_equals(): void
    {
        $this->compare(2, 1, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(false);
        $this->compare(2, 2, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(true);
        $this->compare(1, 2, Comparison::LESSER_THAN_OR_EQUALS)->shouldReturn(true);
    }
}
