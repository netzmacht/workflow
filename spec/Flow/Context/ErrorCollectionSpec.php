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

namespace spec\Netzmacht\Workflow\Flow\Context;

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use PhpSpec\ObjectBehavior;

class ErrorCollectionSpec extends ObjectBehavior
{
    const MESSAGE = 'test %s %s';

    protected static $params = ['foo', 'baar'];

    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorCollection::class);
    }

    function it_adds_error()
    {
        $this->addError(static::MESSAGE, static::$params)->shouldReturn($this);
        $this->getErrors()->shouldContain([static::MESSAGE, static::$params, null]);
    }

    function it_counts_errors()
    {
        $this->countErrors()->shouldReturn(0);
        $this->addError(static::MESSAGE, static::$params);
        $this->countErrors()->shouldReturn(1);
        $this->addError(static::MESSAGE, static::$params);
        $this->countErrors()->shouldReturn(2);
    }

    function it_gets_error_by_index()
    {
        $this->addError(static::MESSAGE, static::$params);
        $this->getError(0)->shouldReturn([static::MESSAGE, static::$params, null]);
    }

    function it_throws_when_unknown_error_index_given()
    {
        $this->shouldThrow('InvalidArgumentException')->during('getError', [0]);
    }

    function it_can_be_reset()
    {
        $this->addError(static::MESSAGE, static::$params);
        $this->hasErrors()->shouldReturn(true);
        $this->reset()->shouldReturn($this);
        $this->hasErrors()->shouldReturn(false);
    }

    function it_adds_multiple_errors(ErrorCollection $errorCollection)
    {
        $errors = [
            [static::MESSAGE, static::$params, null],
            [static::MESSAGE, static::$params, $errorCollection],
        ];

        $allErrors = [
            [static::MESSAGE, static::$params, null],
            [static::MESSAGE, static::$params, null],
            [static::MESSAGE, static::$params, $errorCollection],
        ];

        // make sure it does not override
        $this->addError(static::MESSAGE, static::$params);

        $this->addErrors($errors)->shouldReturn($this);
        $this->countErrors()->shouldReturn(3);
        $this->getErrors()->shouldReturn($allErrors);
    }

    function it_iterates_over_errors()
    {
        $this->shouldHaveType('IteratorAggregate');
        $this->getIterator()->shouldHaveType('Traversable');
    }

    function it_converts_to_array(ErrorCollection $errorCollection)
    {
        $errors = [
            [static::MESSAGE, static::$params, null],
            [static::MESSAGE, static::$params, $errorCollection],
        ];

        $errorCollection->toArray()
            ->shouldBeCalled()
            ->willReturn([[static::MESSAGE, static::$params, null]]);

        $this->addErrors($errors)->shouldReturn($this);

        $this->toArray()->shouldReturn(
            [
                [static::MESSAGE, static::$params, null],
                [
                    static::MESSAGE,
                    static::$params,
                    [
                        [static::MESSAGE, static::$params, null],
                    ],
                ],
            ]
        );
    }
}
