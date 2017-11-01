<?php

namespace spec\Netzmacht\Workflow\Flow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ContextSpec
 * @package spec\Netzmacht\Contao\Workflow\Flow
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

    function it_gets_null_if_property_not_set()
    {
        $this->getProperty('test')->shouldReturn(null);
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

    function it_gets_empty_array_if_namespaced_properties_not_set()
    {
        $this->getProperties('namepsaced')->shouldReturn(array());
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
        $this->setProperty('foo', 'bar', static::CUSTOM_NS);
        $this->getProperties(static::CUSTOM_NS)->shouldReturn(array('foo' => 'bar'));
    }

    function it_sets_param()
    {
        $this->setParam('param', 'val')->shouldReturn($this);
        $this->getParam('param')->shouldReturn('val');
    }

    function it_sets_namespaces_param()
    {
        $this->setParam('param', 'val', 'custom')->shouldReturn($this);
        $this->getParam('param', 'custom')->shouldReturn('val');
    }

    function it_knows_if_param_exists()
    {
        $this->hasParam('param')->shouldReturn(false);
        $this->hasParam('param', 'custom')->shouldReturn(false);

        $this->setParam('param', 'val', 'custom');

        $this->hasParam('param')->shouldReturn(false);
        $this->hasParam('param', 'custom')->shouldReturn(true);
    }

    function it_gets_null_if_param_not_exists()
    {
        $this->getParam('foo')->shouldReturn(null);
    }

    function it_sets_params()
    {
        $data = array('default' => array('foo' => 'bar'));
        $this->setParams($data)->shouldReturn($this);
        $this->getParams()->shouldReturn($data);
    }

    function it_sets_namespaced_params()
    {
        $data = array('default' => array('foo' => 'bar'));
        $this->setParams($data, 'custom')->shouldReturn($this);
        $this->getParams('custom')->shouldReturn($data);
    }

    function it_gets_params_as_array()
    {
        $this->getParams()->shouldBeArray();
    }

    function it_gets_params_contains_namespaces()
    {
        $data = array('default' => array('foo' => 'bar'));
        $this->setParams($data, 'custom');

        $this->getParams()->shouldReturn(array('custom' => $data));
    }
}
