<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;
use PhpSpec\ObjectBehavior;

final class ContextSpec extends ObjectBehavior
{
    public const string CUSTOM_NS = 'custom';

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Context::class);
    }

    public function it_accepts_initial_properties(): void
    {
        $properties = new Properties();
        $this->beConstructedWith($properties);

        $this->getProperties()->shouldBe($properties);
    }

    public function it_accepts_initial_payload(): void
    {
        $payload = new Properties();

        $this->beConstructedWith(null, $payload);

        $this->getPayload()->shouldBe($payload);
    }

    public function it_has_properties(): void
    {
        $this->getProperties()->shouldHaveType(Properties::class);
    }

    public function it_has_payload(): void
    {
        $this->getPayload()->shouldHaveType(Properties::class);
    }

    public function it_has_error_collection(): void
    {
        $this->getErrorCollection()->shouldHaveType(ErrorCollection::class);
    }
}
