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

namespace spec\Netzmacht\Workflow\Flow\Context;

use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransitionSpec
 *
 * @package spec\Netzmacht\Workflow\Flow
 */
class TransitionSpec extends ObjectBehavior
{
    const NAME = 'transition_name';

    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Flow\Context\ErrorCollection';

    protected static $entity = ['id' => 5];

    function let(Workflow $workflow, Step $step)
    {
        $workflow->addTransition(Argument::any())->willReturn($workflow);

        $this->beConstructedWith(static::NAME, $workflow, $step);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Transition');
    }

    function it_behaves_like_base()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Base');
    }

    function it_knows_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_has_actions(Action $action)
    {
        $this->addAction($action)->shouldReturn($this);
        $this->getActions()->shouldReturn([$action]);
    }

    function it_has_post_actions(Action $action)
    {
        $this->addPostAction($action)->shouldReturn($this);
        $this->getPostActions()->shouldReturn([$action]);
    }

    function it_has_a_target_step(Step $step)
    {
        $this->getStepTo()->shouldReturn($step);
    }

    function it_knows_if_input_data_is_not_required(Action $action, Item $item)
    {
        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadParamNames($item)->willReturn([]);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn([]);
    }

    function it_knows_if_input_data_is_required(Action $action, Item $item)
    {
        $this->getRequiredPayloadProperties($item)->shouldReturn([]);

        $action->getRequiredPayloadParamNames($item)->willReturn(['foo']);
        $this->addAction($action);

        $this->getRequiredPayloadProperties($item)->shouldReturn(['foo']);
    }

    function it_checks_a_precondition(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_checks_a_precondition_failing(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);
        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_gets_condition(Condition $condition)
    {
        $this->getCondition()->shouldReturn(null);
        $this->addCondition($condition);
        $this->getCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    function it_gets_pre_condition(Condition $condition)
    {
        $this->getPreCondition()->shouldReturn(null);
        $this->addPreCondition($condition);
        $this->getPreCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AndCondition');
    }

    function it_checks_a_condition(Condition $condition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_checks_a_condition_failing(
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);
        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_is_allowed_by_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);
        $preCondition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($condition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_is_not_allowed_by_failing_pre_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);
        $preCondition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_is_not_allowed_by_failing_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(false);
        $preCondition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_is_available_when_passing_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);
        $preCondition->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_is_not_available_when_condition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $preCondition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context, $errorCollection)
            ->shouldReturn(false);
    }

    function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $condition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(true);

        $preCondition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context, $errorCollection)
            ->shouldReturn(false);
    }

    function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action
    ) {
        $preCondition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(true);

        $condition
            ->match($this, $item, $context, Argument::type(self::ERROR_COLLECTION_CLASS))
            ->willReturn(false);

        $action->getRequiredPayloadParamNames($item)->willReturn(['foo']);
        $this->addAction($action);


        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_executes_actions(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action
    ) {
        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addAction($action);

        $this->executeActions($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_catches_action_failed_exceptions_during_action_execution(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->addAction(new ThrowingAction());

        $errorCollection->addError(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $context->getProperties()->willReturn([]);

        $this->executeActions($item, $context, $errorCollection);
    }

    function it_executes_post_actions(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection,
        Action $action
    ) {
        $action->transit($this, $item, $context)->shouldBeCalled();
        $this->addPostAction($action);

        $this->executePostActions($item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_catches_action_failed_exceptions_during_post_action_execution(
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->addPostAction(new ThrowingAction());

        $errorCollection->addError(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $context->getProperties()->willReturn([]);

        $this->executePostActions($item, $context, $errorCollection);
    }

    function it_has_permission(Permission $permission)
    {
        $permission->equals($permission)->willReturn(true);

        $this->setPermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(true);
        $this->getPermission()->shouldReturn($permission);
    }

    function it_does_not_require_a_permission(Permission $permission)
    {
        $this->getPermission()->shouldReturn(null);
        $this->hasPermission($permission)->shouldReturn(false);
    }
}

class ThrowingAction implements Action
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
        throw new ActionFailedException();
    }
}
