<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Flow\Exception\ActionFailedException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransitionSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin Transition
 */
class TransitionSpec extends ObjectBehavior
{
    const NAME = 'transition_name';

    function let()
    {
        $this->beConstructedWith(static::NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Transition');
    }

    function it_behaves_like_base()
    {
        $this->shouldImplement('Netzmacht\Workflow\Base');
    }

    function it_knows_workflow(Workflow $workflow)
    {
        $this->setWorkflow($workflow)->shouldReturn($this);
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_has_actions(Action $action)
    {
        $this->addAction($action)->shouldReturn($this);
        $this->getActions()->shouldReturn(array($action));
    }

    function it_has_a_target_step(Step $step)
    {
        $this->setStepTo($step)->shouldReturn($this);
        $this->getStepTo()->shouldReturn($step);
    }

    function it_builds_the_form(Form $form, Item $item, Action $action)
    {
        $this->addAction($action);
        $this->buildForm($form, $item)->shouldReturn($this);

        $action->buildForm($form, $item)->shouldBeCalled();
    }

    function it_knows_if_input_data_is_not_required(Action $action)
    {
        $this->requiresInputData()->shouldReturn(false);

        $action->requiresInputData()->willReturn(false);
        $this->addAction($action);

        $this->requiresInputData()->shouldReturn(false);
    }

    function it_knows_if_input_data_is_required(Action $action)
    {
        $this->requiresInputData()->shouldReturn(false);

        $action->requiresInputData()->willReturn(true);
        $this->addAction($action);

        $this->requiresInputData()->shouldReturn(true);
    }

    function it_checks_a_precondition(Condition $condition, Item $item, Context $context)
    {
        $condition->match($this, $item, $context)->willReturn(true);

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(true);
    }

    function it_checks_a_precondition_failing(
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);
        $condition->getError()->shouldBeCalled();

        $this->addPreCondition($condition)->shouldReturn($this);
        $this->checkPreCondition($item, $context)->shouldReturn(false);
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

    function it_checks_a_condition(Condition $condition, Item $item, Context $context)
    {
        $condition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(true);
    }

    function it_checks_a_condition_failing(
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);
        $condition->getError()->shouldBeCalled();

        $this->addCondition($condition)->shouldReturn($this);
        $this->checkCondition($item, $context)->shouldReturn(false);
    }

    function it_is_allowed_by_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($condition);

        $this->isAllowed($item, $context)->shouldReturn(true);
    }

    function it_is_not_allowed_by_failing_pre_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(false);
        $preCondition->getError()->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    function it_is_not_allowed_by_failing_condition(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(false);
        $condition->getError()->shouldBeCalled();

        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAllowed($item, $context)->shouldReturn(false);
    }

    function it_is_available_when_passing_conditions(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition->match($this, $item, $context)->willReturn(true);
        $preCondition->match($this, $item, $context)->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    function it_is_not_available_when_condition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition
            ->match($this, $item, $context)
            ->willReturn(false);

        $condition->getError()->shouldBeCalled();

        $preCondition
            ->match($this, $item, $context)
            ->willReturn(true);

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    function it_is_not_available_when_precondition_fails(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context
    ) {
        $condition
            ->match($this, $item, $context)
            ->willReturn(true);

        $preCondition
            ->match($this, $item, $context)
            ->willReturn(false);

        $preCondition->getError()->shouldBeCalled();

        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this
            ->isAvailable($item, $context)
            ->shouldReturn(false);
    }

    function it_only_recognize_precondition_when_input_is_required(
        Condition $preCondition,
        Condition $condition,
        Item $item,
        Context $context,
        Action $action
    ) {
        $preCondition
            ->match($this, $item, $context)
            ->willReturn(true);

        $condition
            ->match($this, $item, $context)
            ->willReturn(false);

        $action->requiresInputData()->willReturn(true);
        $this->addAction($action);


        $this->addCondition($condition);
        $this->addPreCondition($preCondition);

        $this->isAvailable($item, $context)->shouldReturn(true);
    }

    function it_starts_an_workflow(
        Item $item,
        Entity $entity,
        Context $context,
        Workflow $workflow,
        Step $step,
        ErrorCollection $errorCollection,
        EntityId $entityId
    ) {
        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntity()->willReturn($entity);

        $context->getErrorCollection()->willReturn($errorCollection);
        $context->getProperties()->willReturn(array());

        $entity->getEntityId()->willReturn($entityId);

        $errorCollection->getErrors()->willReturn(array());

        $this->setWorkflow($workflow);
        $this->setStepTo($step);

        $this->start($item, $context)->shouldHaveType('Netzmacht\Workflow\FLow\State');
    }

    function it_transits_an_item(
        Item $item,
        Context $context,
        State $state,
        State $newState
    ) {
        $item->getLatestState()->willReturn($state);
        $item->transit($newState)->shouldBeCalled();

        $state->transit($this, $context, true)->willReturn($newState);

        $context->getProperties()->willReturn(array());

        $this->transit($item, $context)->shouldBe($newState);
    }

    function it_catches_action_failed_exceptions_during_action_execution(
        Item $item,
        Context $context,
        State $state,
        State $newState
    ) {
        $item->getLatestState()->willReturn($state);
        $item->transit($newState)->shouldBeCalled();

        $state->transit($this, $context, false)->willReturn($newState);

        $this->isAllowed($item, $context)->shouldReturn(true);
        $this->addAction(new ThrowingAction());

        $context->addError(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $context->getProperties()->willReturn(array());

        $this->transit($item, $context)->shouldBe($newState);
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
    /**
     * Consider if user input is required.
     *
     * @return bool
     */
    public function requiresInputData()
    {
        // TODO: Implement requiresInputData() method.
    }

    /**
     * Build the corresponding form.
     *
     * @param Form $form Transition form.
     * @param Item $item Workflow item.
     *
     * @return void
     */
    public function buildForm(Form $form, Item $item)
    {
        // TODO: Implement buildForm() method.
    }

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Item       $item       Workflow item.
     * @param Context    $context    Transition context.
     *
     * @throws ActionFailedException
     * @return void
     */
    public function transit(Transition $transition, Item $item, Context $context)
    {
        throw new ActionFailedException();
    }
}
