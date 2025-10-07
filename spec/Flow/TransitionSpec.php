<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransitionSpec extends ObjectBehavior
{
    private const string NAME = 'transition_name';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    public function let(Workflow $workflow, Step $step): void
    {
        $workflow->addTransition(Argument::any())->willReturn($workflow);

        $this->beConstructedWith(self::NAME, $workflow, $step);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Transition');
    }

    public function it_behaves_like_base(): void
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Base');
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

    public function it_has_a_target_step(Step $step): void
    {
        $this->getStepTo()->shouldReturn($step);
    }

    public function it_knows_if_input_data_is_not_required(Action $action, Item $item): void
    {
        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadProperties($item)->willReturn([]);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn([]);
    }

    public function it_knows_if_input_data_is_required(Action $action, Item $item): void
    {
        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn(['foo']);
    }

    public function it_checks_a_precondition(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(true);

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_checks_a_precondition_failing(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(false);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context, $errorCollection)->shouldReturn(false);
    }

    public function it_gets_condition(Condition $condition): void
    {
        $this->getCondition()->shouldReturn(null);
        $this->addCondition($condition);
        $this->getCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    public function it_gets_pre_condition(Condition $condition): void
    {
        $this->getPreCondition()->shouldReturn(null);
        $this->addPreCondition($condition);
        $this->getPreCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    public function it_checks_a_condition(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_checks_a_condition_failing(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(false);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context, $errorCollection)->shouldReturn(false);
    }

    public function it_is_allowed_by_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($condition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_is_not_allowed_by_failing_pre_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(false);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(false);
    }

    public function it_is_not_allowed_by_failing_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition->match($this, $item, $context)->willReturn(false);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(false);
    }

    public function it_is_available_when_passing_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);

        $condition->match($this->getWrappedObject(), $item, $context)->willReturn(true);
        $preCondition->match($this->getWrappedObject(), $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_is_not_available_when_condition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $condition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(false);

        $preCondition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this
            ->isAvailable($item, $context, $errorCollection)
            ->shouldReturn(false);
    }

    public function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        Action $action,
        ErrorCollection $errorCollection,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);
        $context->getErrorCollection()->willReturn($errorCollection);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $condition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(true);

        $preCondition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(false);

        $context->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context, $errorCollection)
            ->shouldReturn(false);
    }

    public function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action,
    ): void {
        $context->createCleanCopy(Argument::any())->willReturn($context);

        $preCondition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(true);

        $condition
            ->match($this->getWrappedObject(), $item, $context)
            ->willReturn(false);

        $action->getRequiredPayloadProperties($item)->willReturn(['foo']);
        $this->addAction($action);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_executes(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action,
        Action $postAction,
        State $state,
    ): void {
        $this->addAction($action);
        $this->addPostAction($postAction);

        $context->getErrorCollection()
            ->shouldBeCalled()
            ->willReturn($errorCollection);

        $errorCollection->hasErrors()
            ->shouldBeCalled()
            ->willReturn(false);

        $item->isWorkflowStarted()
            ->shouldBeCalled()
            ->willReturn(true);

        $item->getLatestStateOccurred()
            ->shouldBeCalled()
            ->willReturn($state);

        $action->transit($this->getWrappedObject(), $item, $context)
            ->shouldBeCalledOnce();

        $postAction->transit($this->getWrappedObject(), $item, $context)
            ->shouldBeCalledOnce();

        $item->transit(Argument::type(Transition::class), $context, true)
            ->shouldBeCalledOnce();

        $this->execute($item, $context)->shouldReturn($state);
    }

    public function it_executes_actions(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action,
    ): void {
        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addAction($action);

        $context->getErrorCollection()->willReturn($errorCollection);
        $errorCollection->hasErrors()->willReturn(false);

        $this->executeActions($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_catches_action_failed_exceptions_during_action_execution(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $this->addAction($this->throwingAction());

        $context->getProperties()->willReturn([]);

        $context
            ->addError('transition.action.failed', Argument::type('array'), Argument::any())
            ->shouldBeCalled();

        $context->getErrorCollection()->willReturn($errorCollection);

        $this->executeActions($item, $context, $errorCollection);
    }

    public function it_executes_post_actions(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action,
    ): void {
        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addPostAction($action);

        $context->getErrorCollection()->willReturn($errorCollection);
        $errorCollection->hasErrors()->willReturn(false);

        $this->executePostActions($item, $context, $errorCollection)->shouldReturn(true);
    }

    public function it_catches_action_failed_exceptions_during_post_action_execution(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
    ): void {
        $this->addPostAction($this->throwingAction());

        $context->getProperties()->willReturn([]);

        $context
            ->addError('transition.action.failed', Argument::type('array'), Argument::any())
            ->shouldBeCalled();

        $this->executePostActions($item, $context, $errorCollection);
    }

    public function it_has_permission(Permission $permission): void
    {
        $permission->equals($permission)->willReturn(true);

        $this->setPermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(true);
        $this->getPermission()->shouldReturn($permission);
    }

    public function it_does_not_require_a_permission(Permission $permission): void
    {
        $this->getPermission()->shouldReturn(null);
        $this->hasPermission($permission)->shouldReturn(false);
    }

    private function throwingAction(): Action
    {
        return new class implements Action
        {
            /** {@inheritDoc} */
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
                throw new ActionFailedException();
            }
        };
    }
}
