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

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Base;
use PhpSpec\ObjectBehavior;

/**
 * Class BaseSpec
 *
 * @package spec\Netzmacht\Workflow
 */
class BaseSpec extends ObjectBehavior
{
    const NAME = 'test';
    const LABEL = 'label';
    const ID = 5;

    function let()
    {
        $this->beAnInstanceOf(BaseExample::class);
        $this->beConstructedWith(static::NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Base::class);
    }

    function it_accepts_initial_config()
    {
        $this->beConstructedWith(static::NAME, '', ['config' => 'test']);
        $this->getConfig()->shouldBe(['config' => 'test']);
    }

    function it_accepts_initial_label()
    {
        $this->beConstructedWith(static::NAME, static::LABEL);
        $this->getLabel()->shouldBe(static::LABEL);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn(static::NAME);
    }

    function it_has_a_label()
    {
        $this->setLabel(static::LABEL)->shouldReturn($this);
        $this->getLabel()->shouldReturn(static::LABEL);
    }

    function it_uses_name_as_label_if_no_label_given()
    {
        $this->getLabel()->shouldReturn(static::NAME);
    }

    function it_has_config_values()
    {
        $this->hasConfigValue('config')->shouldReturn(false);
        $this->setConfigValue('config', 'test')->shouldReturn($this);
        $this->getConfigValue('config')->shouldReturn('test');
        $this->hasConfigValue('config')->shouldReturn(true);
    }

    function it_accepts_an_default_value_for_nonexisting_config_values()
    {
        $this->getConfigValue('config', 'bar')->shouldReturn('bar');
    }

    function it_adds_multiple_config_values()
    {
        $this->addConfig(['config' => 'foo', 'test' => 'bar'])->shouldReturn($this);
        $this->getConfigValue('config')->shouldReturn('foo');
        $this->getConfigValue('test')->shouldReturn('bar');
    }

    function it_removes_a_config_value()
    {
        $this->setConfigValue('config', 'test');
        $this->hasConfigValue('config')->shouldReturn(true);
        $this->removeConfigValue('config')->shouldReturn($this);
        $this->hasConfigValue('config')->shouldReturn(false);
    }

    function it_returns_config()
    {
        $this->setConfigValue('config', 'test');
        $this->getConfig()->shouldBe(['config' => 'test']);
    }
}

class BaseExample extends Base
{

}
