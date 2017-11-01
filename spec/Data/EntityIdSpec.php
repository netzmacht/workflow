<?php

namespace spec\Netzmacht\Workflow\Data;

use Netzmacht\Workflow\Data\EntityId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class EntityIdSpec
 * @package spec\Netzmacht\Workflow\Data
 * @mixin EntityId
 */
class EntityIdSpec extends ObjectBehavior
{
    const PROVIDER_NAME = 'provider_example';

    const IDENTIFIER = 10;

    function let()
    {
        $this->beConstructedThrough('fromProviderNameAndId', array(static::PROVIDER_NAME, static::IDENTIFIER));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Data\EntityId');
    }

    function it_has_an_identifier()
    {
        $this->getIdentifier()->shouldReturn(static::IDENTIFIER);
    }

    function it_has_a_provider_name()
    {
        $this->getProviderName()->shouldReturn(static::PROVIDER_NAME);
    }

    function it_equals_to_same_entity_id(EntityId $otherEntitdId)
    {
        $otherEntitdId->__toString()->willReturn(static::PROVIDER_NAME . '::' . static::IDENTIFIER);

        $this->equals($otherEntitdId)->shouldReturn(true);
    }

    function it_does_not_equals_to_another_entity_id_with_different_id(EntityId $otherEntitdId)
    {
        $otherEntitdId->__toString()->willReturn(static::PROVIDER_NAME . '::' . (static::IDENTIFIER + 5));

        $this->equals($otherEntitdId)->shouldReturn(false);
    }

    function it_does_not_equals_to_another_entity_id_with_different_provider_name(EntityId $otherEntitdId)
    {
        $otherEntitdId->__toString()->willReturn(static::PROVIDER_NAME . '_2::' . static::IDENTIFIER);

        $this->equals($otherEntitdId)->shouldReturn(false);
    }

    function it_casts_to_string()
    {
        $this->__toString()->shouldReturn(static::PROVIDER_NAME . '::' . static::IDENTIFIER);
    }

    function it_parses_string_representation()
    {
        $this->beConstructedThrough('fromString', array(static::PROVIDER_NAME . '::' . static::IDENTIFIER));

        $this->getIdentifier()->shouldReturn(static::IDENTIFIER);
        $this->getProviderName()->shouldReturn(static::PROVIDER_NAME);
    }

    function it_constructs_from_scalars()
    {
        $this->beConstructedThrough('fromProviderNameAndId', array(static::PROVIDER_NAME, static::IDENTIFIER));

        $this->getIdentifier()->shouldReturn(static::IDENTIFIER);
        $this->getProviderName()->shouldReturn(static::PROVIDER_NAME);
    }
}
