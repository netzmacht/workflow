<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow\Context;

use Netzmacht\Workflow\Flow\Context\Properties;
use PhpSpec\ObjectBehavior;

class PropertiesSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Properties::class);
    }

    public function it_gets_a_property_value(): void
    {
        $this->set('foo', 'bar')->shouldReturn($this);
        $this->get('foo')->shouldReturn('bar');
    }

    public function it_knows_if_an_property_exist(): void
    {
        $this->has('foo')->shouldReturn(false);

        $this->set('foo', 'bar')->shouldReturn($this);
        $this->has('foo')->shouldReturn(true);
    }

    public function it_gets_null_if_property_not_exist(): void
    {
        $this->has('foo')->shouldReturn(false);
        $this->get('foo')->shouldReturn(null);
    }

    public function it_converts_to_array(): void
    {
        $this->set('foo', 'bar');
        $this->toArray()->shouldReturn(['foo' => 'bar']);
    }

    public function it_accepts_properties_when_being_constructed(): void
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->toArray()->shouldReturn(['foo' => 'bar']);
    }
}
