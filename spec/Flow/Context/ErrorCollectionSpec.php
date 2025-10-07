<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Context;

use Countable;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use PhpSpec\ObjectBehavior;

class ErrorCollectionSpec extends ObjectBehavior
{
    public const string MESSAGE = 'test %s %s';

    /** @var list<string> */
    protected static array $params = ['foo', 'baar'];

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ErrorCollection::class);
    }

    public function it_is_countable(): void
    {
        $this->shouldImplement(Countable::class);
    }

    public function it_adds_error(): void
    {
        $this->addError(self::MESSAGE, static::$params)->shouldReturn($this);
        $this->getErrors()->shouldContain([self::MESSAGE, static::$params, null]);
    }

    public function it_counts_errors(): void
    {
        $this->countErrors()->shouldReturn(0);
        $this->count()->shouldReturn(0);
        $this->addError(self::MESSAGE, static::$params);
        $this->countErrors()->shouldReturn(1);
        $this->count()->shouldReturn(1);
        $this->addError(self::MESSAGE, static::$params);
        $this->countErrors()->shouldReturn(2);
        $this->count()->shouldReturn(2);
    }

    public function it_gets_error_by_index(): void
    {
        $this->addError(self::MESSAGE, static::$params);
        $this->getError(0)->shouldReturn([self::MESSAGE, static::$params, null]);
    }

    public function it_throws_when_unknown_error_index_given(): void
    {
        $this->shouldThrow('InvalidArgumentException')->during('getError', [0]);
    }

    public function it_can_be_reset(): void
    {
        $this->addError(self::MESSAGE, static::$params);
        $this->hasErrors()->shouldReturn(true);
        $this->reset()->shouldReturn($this);
        $this->hasErrors()->shouldReturn(false);
    }

    public function it_adds_multiple_errors(ErrorCollection $errorCollection): void
    {
        $errors = [
            [self::MESSAGE, static::$params, null],
            [self::MESSAGE, static::$params, $errorCollection],
        ];

        $allErrors = [
            [self::MESSAGE, static::$params, null],
            [self::MESSAGE, static::$params, null],
            [self::MESSAGE, static::$params, $errorCollection],
        ];

        // make sure it does not override
        $this->addError(self::MESSAGE, static::$params);

        $this->addErrors($errors)->shouldReturn($this);
        $this->countErrors()->shouldReturn(3);
        $this->getErrors()->shouldReturn($allErrors);
    }

    public function it_iterates_over_errors(): void
    {
        $this->shouldHaveType('IteratorAggregate');
        $this->getIterator()->shouldHaveType('Traversable');
    }

    public function it_converts_to_array(ErrorCollection $errorCollection): void
    {
        $errors = [
            [self::MESSAGE, static::$params, null],
            [self::MESSAGE, static::$params, $errorCollection],
        ];

        $errorCollection->toArray()
            ->shouldBeCalled()
            ->willReturn([[self::MESSAGE, static::$params, null]]);

        $this->addErrors($errors)->shouldReturn($this);

        $this->toArray()->shouldReturn(
            [
                [self::MESSAGE, static::$params, null],
                [
                    self::MESSAGE,
                    static::$params,
                    [
                        [self::MESSAGE, static::$params, null],
                    ],
                ],
            ],
        );
    }
}
