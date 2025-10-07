<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Data;

use Netzmacht\Workflow\Data\EntityId;
use PhpSpec\ObjectBehavior;

class EntityIdSpec extends ObjectBehavior
{
    public const string PROVIDER_NAME = 'provider_example';

    public const int IDENTIFIER = 10;

    public function let(): void
    {
        $this->beConstructedThrough('fromProviderNameAndId', [self::PROVIDER_NAME, self::IDENTIFIER]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(EntityId::class);
    }

    public function it_has_an_identifier(): void
    {
        $this->getIdentifier()
            ->shouldReturn(self::IDENTIFIER);
    }

    public function it_has_a_provider_name(): void
    {
        $this->getProviderName()
            ->shouldReturn(self::PROVIDER_NAME);
    }

    public function it_equals_to_same_entity_id(): void
    {
        $otherEntityId = EntityId::fromString(self::PROVIDER_NAME . '::' . self::IDENTIFIER);

        $this->equals($otherEntityId)
            ->shouldReturn(true);
    }

    public function it_does_not_equals_to_another_entity_id_with_different_id(): void
    {
        $otherEntityId = EntityId::fromString(self::PROVIDER_NAME . '::' . (self::IDENTIFIER + 5));

        $this->equals($otherEntityId)
            ->shouldReturn(false);
    }

    public function it_does_not_equals_to_another_entity_id_with_different_provider_name(): void
    {
        $otherEntityId = EntityId::fromString(self::PROVIDER_NAME . '_2::' . self::IDENTIFIER);

        $this->equals($otherEntityId)
            ->shouldReturn(false);
    }

    public function it_casts_to_string(): void
    {
        $this->__toString()
            ->shouldReturn(self::PROVIDER_NAME . '::' . self::IDENTIFIER);
    }

    public function it_parses_string_representation(): void
    {
        $this->beConstructedThrough('fromString', [self::PROVIDER_NAME . '::' . self::IDENTIFIER]);

        $this->getIdentifier()
            ->shouldReturn(self::IDENTIFIER);

        $this->getProviderName()
            ->shouldReturn(self::PROVIDER_NAME);
    }

    public function it_constructs_from_scalars(): void
    {
        $this->beConstructedThrough('fromProviderNameAndId', [self::PROVIDER_NAME, self::IDENTIFIER]);

        $this->getIdentifier()
            ->shouldReturn(self::IDENTIFIER);

        $this->getProviderName()
            ->shouldReturn(self::PROVIDER_NAME);
    }
}
