<?php

namespace spec\Netzmacht\Workflow\Flow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ContextSpec
 * @package spec\Netzmacht\Contao\Workflow\Flow
 * @mixin \Netzmacht\Workflow\Flow\Context
 */
class ContextSpec extends ObjectBehavior
{
    const CUSTOM_NS = 'custom';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Context');
    }

    function it_accepts_initial_properties()
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith($data);

        $this->getProperties()->shouldBe($data);
    }

    function it_accepts_initial_params()
    {
        $data = array('foo' => 'bar');
        $this->beConstructedWith(array(), $data);

        $this->getParams()->shouldBe($data);
    }

    function it_sets_property()
    {
        $this->setProperty('prop', 'val')->shouldReturn($this);
        $this->getProperty('prop')->shouldReturn('val');
    }

    function it_knows_if_property_exists()
    {
        $this->hasProperty('prop')->shouldReturn(false);
        $this->setProperty('prop', 'val');
        $this->hasProperty('prop')->shouldReturn(true);
    }

    function it_gets_properties_as_array()
    {
        $this->getProperties()->shouldBeArray();
    }


    function it_sets_namespaced_property()
    {
        $this->setProperty('prop', 'val', static::CUSTOM_NS)->shouldReturn($this);
        $this->getProperty('prop', static::CUSTOM_NS)->shouldReturn('val');
        $this->hasProperty('prop')->shouldReturn(false);
    }

    function it_knows_if_property_exists_in_a_namespace()
    {
        $this->hasProperty('prop', static::CUSTOM_NS)->shouldReturn(false);
        $this->setProperty('prop', 'val', static::CUSTOM_NS);
        $this->hasProperty('prop', static::CUSTOM_NS)->shouldReturn(true);
        $this->hasProperty('prop')->shouldReturn(false);
    }

    function it_gets_namespace_properties_as_array()
    {
        $this->getProperties(static::CUSTOM_NS)->shouldBeArray();
    }

    function it_sets_param()
    {
        $this->setParam('param', 'val')->shouldReturn($this);
        $this->getParam('param')->shouldReturn('val');
    }

    function it_knows_if_param_exists()
    {
        $this->hasParam('param')->shouldReturn(false);
        $this->setParam('param', 'val');
        $this->hasParam('param')->shouldReturn(true);
    }

    function it_gets_params_as_array()
    {
        $this->getParams()->shouldBeArray();
    }

    function it_add_an_errors()
    {
        $this->hasErrors()->shouldReturn(false);
        $this->addError('error', array('param' => 'foo'))->shouldReturn($this);
        $this->hasErrors()->shouldReturn(true);
    }

    function it_gets_error_collection()
    {
        $this->getErrorCollection()->shouldBeAnInstanceOf('Netzmacht\Workflow\Data\ErrorCollection');
    }
}
