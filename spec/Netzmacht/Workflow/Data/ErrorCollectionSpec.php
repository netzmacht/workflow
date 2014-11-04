<?php

namespace spec\Netzmacht\Workflow\Data;

use Netzmacht\Contao\Workflow\ErrorCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorCollectionSpec extends ObjectBehavior
{
    const MESSAGE = 'test %s %s';

    protected static $params = array('foo', 'baar');

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Data\ErrorCollection');
    }

    function it_adds_error()
    {
        $this->addError(static::MESSAGE, static::$params)->shouldReturn($this);
        $this->getErrors()->shouldContain(array(static::MESSAGE, static::$params));
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
        $this->getError(0)->shouldReturn(array(static::MESSAGE, static::$params));
    }

    function it_throws_when_unknown_error_index_given()
    {
        $this->shouldThrow('InvalidArgumentException')->during('getError', array(0));
    }

    function it_can_be_reset()
    {
        $this->addError(static::MESSAGE, static::$params);
        $this->hasErrors()->shouldReturn(true);
        $this->reset()->shouldReturn($this);
        $this->hasErrors()->shouldReturn(false);
    }

    function it_adds_multiple_errors()
    {
        $errors = array(
            array(static::MESSAGE, static::$params),
            array(static::MESSAGE, static::$params),
        );

        $allErrors = array(
            array(static::MESSAGE, static::$params),
            array(static::MESSAGE, static::$params),
            array(static::MESSAGE, static::$params),
        );

        // make sure it does not override
        $this->addError(static::MESSAGE, static::$params);

        $this->addErrors($errors)->shouldReturn($this);
        $this->countErrors()->shouldReturn(3);
        $this->getErrors()->shouldReturn($allErrors);
    }
}
