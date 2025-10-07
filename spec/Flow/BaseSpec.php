<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Base;
use PhpSpec\ObjectBehavior;

class BaseSpec extends ObjectBehavior
{
    public const string NAME  = 'test';
    public const string LABEL = 'label';
    public const int ID       = 5;

    public function let(): void
    {
        $this->beConstructedWith(self::NAME);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Base::class);
    }

    public function it_accepts_initial_config(): void
    {
        $this->beConstructedWith(self::NAME, '', ['config' => 'test']);
        $this->getConfig()->shouldBe(['config' => 'test']);
    }

    public function it_accepts_initial_label(): void
    {
        $this->beConstructedWith(self::NAME, self::LABEL);
        $this->getLabel()->shouldBe(self::LABEL);
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn(self::NAME);
    }

    public function it_has_a_label(): void
    {
        $this->setLabel(self::LABEL)->shouldReturn($this);
        $this->getLabel()->shouldReturn(self::LABEL);
    }

    public function it_uses_name_as_label_if_no_label_given(): void
    {
        $this->getLabel()->shouldReturn(self::NAME);
    }

    public function it_has_config_values(): void
    {
        $this->hasConfigValue('config')->shouldReturn(false);
        $this->setConfigValue('config', 'test')->shouldReturn($this);
        $this->getConfigValue('config')->shouldReturn('test');
        $this->hasConfigValue('config')->shouldReturn(true);
    }

    public function it_accepts_an_default_value_for_nonexisting_config_values(): void
    {
        $this->getConfigValue('config', 'bar')->shouldReturn('bar');
    }

    public function it_adds_multiple_config_values(): void
    {
        $this->addConfig(['config' => 'foo', 'test' => 'bar'])->shouldReturn($this);
        $this->getConfigValue('config')->shouldReturn('foo');
        $this->getConfigValue('test')->shouldReturn('bar');
    }

    public function it_removes_a_config_value(): void
    {
        $this->setConfigValue('config', 'test');
        $this->hasConfigValue('config')->shouldReturn(true);
        $this->removeConfigValue('config')->shouldReturn($this);
        $this->hasConfigValue('config')->shouldReturn(false);
    }

    public function it_returns_config(): void
    {
        $this->setConfigValue('config', 'test');
        $this->getConfig()->shouldBe(['config' => 'test']);
    }
}
