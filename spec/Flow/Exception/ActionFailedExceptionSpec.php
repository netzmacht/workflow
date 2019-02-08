<?php

namespace spec\Netzmacht\Workflow\Flow\Exception;

use function get_class;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

final class ActionFailedExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ActionFailedException::class);
    }

    public function it_is_a_flow_exception(): void
    {
        $this->shouldBeAnInstanceOf(FlowException::class);
    }

    public function it_has_no_action_name_by_default(): void
    {
        $this->actionName()->shouldReturn(null);
    }

    public function it_has_no_error_collection_by_default(): void
    {
        $this->errorCollection()->shouldReturn(null);
    }

    public function it_is_instantiable_with_action_name(): void
    {
        $this->beConstructedThrough('namedAction', ['foo']);

        $this->getMessage()->shouldReturn('Execution of action "foo" failed.');
        $this->actionName()->shouldReturn('foo');
    }

    public function it_is_instantiable_with_action(Action $action): void
    {
        $parts = explode('\\', trim(get_class($action->getWrappedObject()), '\\'));
        $actionName = end($parts);

        $this->beConstructedThrough('action', [$action]);

        $this->getMessage()->shouldReturn('Execution of action "' . $actionName . '" failed.');
        $this->actionName()->shouldReturn($actionName);
    }

    public function it_is_instantiable_with_labelled_action(): void
    {
        $action = new class('Foo', 'foo') extends Base implements Action
        {
            public function getRequiredPayloadProperties(Item $item): array
            {
                return [];
            }

            public function validate(Item $item, Context $context): bool
            {
                return true;
            }

            public function transit(Transition $transition, Item $item, Context $context): void
            {

            }
        };

        $this->beConstructedThrough('action', [$action]);

        $this->getMessage()->shouldReturn('Execution of action "foo" failed.');
        $this->actionName()->shouldReturn('foo');
    }

    public function it_allows_error_collection_when_instantiated_with_named_action(ErrorCollection $collection): void
    {
        $this->beConstructedThrough('namedAction', ['foo', $collection]);

        $this->errorCollection()->shouldReturn($collection);
    }

    public function it_allows_error_collection_when_instantiated_with_action(
        Action $action,
        ErrorCollection $collection
    ): void {
        $this->beConstructedThrough('action', [$action, $collection]);

        $this->errorCollection()->shouldReturn($collection);
    }
}
