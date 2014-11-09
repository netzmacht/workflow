<?php

namespace spec\Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Factory\Event\CreateEntityEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class CreateEntityEventSpec
 * @package spec\Netzmacht\Workflow\Factory\Event
 * @mixin CreateEntityEvent
 */
class CreateEntityEventSpec extends ObjectBehavior
{
    const PROVIDER = 'table';

    protected  $model = array(
        'id' => 5,
        'name' => 'model'
    );

    function let()
    {
        $this->beConstructedWith($this->model, static::PROVIDER);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Factory\Event\CreateEntityEvent');
    }

    function it_is_an_event()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\Event');
    }

    function it_has_the_model()
    {
        $this->getModel()->shouldReturn($this->model);
    }

    function it_has_optional_a_provider_name()
    {
        $this->getProviderName()->shouldReturn(static::PROVIDER);
    }

    function it_does_not_require_a_provider()
    {
        $this->beConstructedWith($this->model);
        $this->getProviderName()->shouldReturn(null);
    }

    function it_sets_the_entity(Entity $entity)
    {
        $this->setEntity($entity)->shouldReturn($this);
        $this->getEntity()->shouldReturn($entity);
    }
}
