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

use Netzmacht\Workflow\Flow\Context\Properties;
use PhpSpec\ObjectBehavior;

class PropertiesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Properties::class);
    }

    function it_gets_a_property_value()
    {
        $this->set('foo', 'bar')->shouldReturn($this);
        $this->get('foo')->shouldReturn('bar');
    }

    function it_knows_if_an_property_exist()
    {
        $this->has('foo')->shouldReturn(false);

        $this->set('foo', 'bar')->shouldReturn($this);
        $this->has('foo')->shouldReturn(true);
    }

    function it_gets_null_if_property_not_exist()
    {
        $this->has('foo')->shouldReturn(false);
        $this->get('foo')->shouldReturn(null);
    }

    function it_converts_to_array()
    {
        $this->set('foo', 'bar');
        $this->toArray()->shouldReturn(['foo' => 'bar']);
    }

    function it_accepts_properties_when_being_constructed()
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->toArray()->shouldReturn(['foo' => 'bar']);
    }
}
