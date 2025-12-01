<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use DateTimeImmutable;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Condition\Transition\AndCondition;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Override;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class TransitionSpec extends ObjectBehavior
{
    private const string NAME = 'transition_name';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    private Step $step;

    private State $state;

    public function let(Workflow $workflow): void
    {
        $workflow->addTransition(Argument::any())->willReturn($workflow);

        $this->step  = new Step('step');
        $this->state = new State(
            EntityId::fromProviderNameAndId('test', 5),
            'workflow',
            'start',
            'step',
            true,
            [],
            new DateTimeImmutable(),
        );

        $this->beConstructedWith(self::NAME, $workflow, $this->step);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Transition::class);
    }

    public function it_behaves_like_base(): void
    {
        $this->shouldImplement(Base::class);
    }

    public function it_knows_workflow(Workflow $workflow): void
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    public function it_has_actions(Action $action): void
    {
        $this->addAction($action)->shouldReturn($this);
        $this->getActions()->shouldReturn([$action]);
    }

    public function it_has_post_actions(Action $action): void
    {
        $this->addPostAction($action)->shouldReturn($this);
        $this->getPostActions()->shouldReturn([$action]);
    }

    public function it_has_a_target_step(): void
    {
        $this->getStepTo()->shouldReturn($this->step);
    }

    public function it_knows_if_input_data_is_not_required(Action $action): void
    {
        $item = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadProperties($item)->willReturn([]);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn([]);
    }

    public function it_knows_if_input_data_is_required(Action $action): void
    {
        $item = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn(['foo']);
    }

    public function it_checks_a_precondition(Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(true);

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(true);
    }

    public function it_checks_a_precondition_failing(Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(false);

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(false);
    }

    public function it_gets_condition(Condition $condition): void
    {
        $this->getCondition()->shouldReturn(null);
        $this->addCondition($condition);
        $this->getCondition()->shouldHaveType(AndCondition::class);
    }

    public function it_gets_pre_condition(Condition $condition): void
    {
        $this->getPreCondition()->shouldReturn(null);
        $this->addPreCondition($condition);
        $this->getPreCondition()->shouldHaveType(AndCondition::class);
    }

    public function it_checks_a_condition(Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(true);
    }

    public function it_checks_a_condition_failing(Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(false);

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(false);
    }

    public function it_is_allowed_by_conditions(Condition $preCondition, Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($condition);

        $this->isAllowed($item, $context)->shouldReturn(true);
    }

    public function it_is_not_allowed_by_failing_pre_condition(Condition $preCondition, Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(false);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    public function it_is_not_allowed_by_failing_condition(Condition $preCondition, Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this, $item, $context)->willReturn(false);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    public function it_is_available_when_passing_conditions(Condition $preCondition, Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition->match($this->getWrappedObject(), $item, Argument::type(Context::class))->willReturn(true);
        $preCondition->match($this->getWrappedObject(), $item, Argument::type(Context::class))->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    public function it_is_not_available_when_condition_fails(Condition $preCondition, Condition $condition): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $condition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(false);

        $preCondition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    public function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Action $action,
    ): void {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $condition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(true);

        $preCondition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(false);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    public function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Action $action,
    ): void {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $preCondition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(true);

        $condition
            ->match($this->getWrappedObject(), $item, Argument::type(Context::class))
            ->willReturn(false);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    public function it_executes(Action $action, Action $postAction): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $newState = new State(
            EntityId::fromProviderNameAndId('test', 5),
            'workflow',
            'transition',
            'target',
            true,
            [],
            new DateTimeImmutable(),
        );

        $this->addAction($action);
        $this->addPostAction($postAction);

        $action->transit($this->getWrappedObject(), $item, $context)
            ->shouldBeCalledOnce();

        $postAction->transit($this->getWrappedObject(), $item, $context)
            ->shouldBeCalledOnce();

        $this->execute($item, $context)->shouldReturn($newState);
    }

    public function it_executes_actions(Action $action): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $newState = new State(
            EntityId::fromProviderNameAndId('test', 5),
            'workflow',
            'transition',
            'target',
            true,
            [],
            new DateTimeImmutable(),
        );

        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addAction($action);

        $this->execute($item, $context)->shouldReturn($newState);
    }

    public function it_catches_action_failed_exceptions_during_action_execution(): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $this->addAction($this->throwingAction());

        $this->execute($item, $context);
    }

    public function it_executes_post_actions(Action $action): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $newState = new State(
            EntityId::fromProviderNameAndId('test', 5),
            'workflow',
            'transition',
            'target',
            true,
            [],
            new DateTimeImmutable(),
        );

        $item->getLatestStateOccurred()->willReturn($this->state, $newState);
        $item->isWorkflowStarted()->willReturn(true);
        $item->transit($this->getWrappedObject(), $context, true)->willReturn($newState);

        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addPostAction($action);

        $this->execute($item, $context)->shouldReturn($newState);
    }

    public function it_catches_action_failed_exceptions_during_post_action_execution(): void
    {
        $context = new Context();
        $item    = Item::initialize(EntityId::fromProviderNameAndId('test', 5), ['id' => 5]);

        $newState = new State(
            EntityId::fromProviderNameAndId('test', 5),
            'workflow',
            'transition',
            'target',
            true,
            [],
            new DateTimeImmutable(),
        );

        $item->getLatestStateOccurred()->willReturn($this->state, $newState);
        $item->isWorkflowStarted()->willReturn(true);
        $item->transit($this->getWrappedObject(), $context, true)->willReturn($newState);

        $this->addPostAction($this->throwingAction());

        $this->execute($item, $context);
    }

    public function it_has_permission(): void
    {
        $permission = Permission::fromString('workflow:permission');

        $this->setPermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(true);
        $this->getPermission()->shouldReturn($permission);
    }

    public function it_does_not_require_a_permission(): void
    {
        $permission = Permission::fromString('workflow:permission');

        $this->getPermission()->shouldReturn(null);
        $this->hasPermission($permission)->shouldReturn(false);
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    private function throwingAction(): Action
    {
        return new class implements Action
        {
            /** {@inheritDoc} */
            #[Override]
            public function getRequiredPayloadProperties(Item $item): array
            {
                return [];
            }

            #[Override]
            public function validate(Item $item, Context $context): bool
            {
                return true;
            }

            #[Override]
            public function transit(Transition $transition, Item $item, Context $context): void
            {
                throw new ActionFailedException();
            }
        };
    }
}
