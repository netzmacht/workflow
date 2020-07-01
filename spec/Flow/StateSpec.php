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

namespace spec\Netzmacht\Workflow\Flow;

use DateTimeImmutable;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

/**
 * Class StateSpec
 *
 * @package spec\Netzmacht\Workflow\Flow
 */
class StateSpec extends ObjectBehavior
{
    private const WORKFLOW_NAME = 'workflow_name';
    private const TARGET_WORKFLOW_NAME = 'workflow_name_target';
    private const TRANSITION_NAME = 'transition_name';
    private const STEP_TO = 'step_to';
    private const STATE_ID = 121;

    private static $data = [
        'foo' => true,
        'bar' => false,
    ];

    /**
     * @var EntityId
     */
    private $entityId;

    private static $errors = [['error.message', []]];

    function let(\DateTimeImmutable $dateTime)
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);

        $this->beConstructedWith(
            $this->entityId,
            static::WORKFLOW_NAME,
            static::TRANSITION_NAME,
            static::STEP_TO,
            true,
            static::$data,
            $dateTime,
            static::$errors,
            static::STATE_ID,
            self::TARGET_WORKFLOW_NAME
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(State::class);
    }

    function it_knows_current_step()
    {
        $this->getStepName()->shouldReturn(static::STEP_TO);
    }

    function it_knows_last_transition()
    {
        $this->getTransitionName()->shouldReturn(static::TRANSITION_NAME);
    }

    function it_knows_reached_time()
    {
        $this->getReachedAt()->shouldBeAnInstanceOf(\DateTimeImmutable::class);
    }

    function it_stores_data()
    {
        $this->getData()->shouldReturn(static::$data);
    }

    function it_knows_entity_id()
    {
        $this->getEntityId()->shouldReturn($this->entityId);
    }

    function it_stores_error()
    {
        $this->getErrors()->shouldReturn(static::$errors);
    }

    function it_has_an_id()
    {
        $this->getStateId()->shouldReturn(static::STATE_ID);
    }

    function it_knows_start_workflow_name()
    {
        $this->getStartWorkflowName()->shouldReturn(static::WORKFLOW_NAME);
    }

    function it_knows_target_workflow_name()
    {
        $this->getTargetWorkflowName()->shouldReturn(static::TARGET_WORKFLOW_NAME);
    }

    function it_knows_workflow_name()
    {
        $this->getWorkflowName()->shouldReturn(static::TARGET_WORKFLOW_NAME);
    }

    function it_uses_start_workflow_as_target_workflow_if_target_workflow_is_not_set()
    {
        $this->beConstructedWith(
            $this->entityId,
            static::WORKFLOW_NAME,
            static::TRANSITION_NAME,
            static::STEP_TO,
            true,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            static::STATE_ID
        );

        $this->getTargetWorkflowName()->shouldReturn(static::WORKFLOW_NAME);
    }

    function it_uses_start_workflow_as_current_workflow_for_not_successful_states()
    {
        $this->beConstructedWith(
            $this->entityId,
            static::WORKFLOW_NAME,
            static::TRANSITION_NAME,
            static::STEP_TO,
            false,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            static::STATE_ID,
            static::TARGET_WORKFLOW_NAME
        );

        $this->getWorkflowName()->shouldReturn(static::WORKFLOW_NAME);
    }

    function it_uses_target_workflow_as_current_workflow_for_successful_states()
    {
        $this->beConstructedWith(
            $this->entityId,
            static::WORKFLOW_NAME,
            static::TRANSITION_NAME,
            static::STEP_TO,
            true,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            static::STATE_ID,
            static::TARGET_WORKFLOW_NAME
        );

        $this->getWorkflowName()->shouldReturn(static::TARGET_WORKFLOW_NAME);
    }

    function it_constructs_with_start(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) : void {
        $stepTo->getName()->willReturn(self::STEP_TO);

        $transition->getWorkflow()->willReturn($workflow);
        $transition->getStepTo()->willReturn($stepTo);

        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->getErrors()
            ->willReturn([]);

        $this->beConstructedThrough(
            'start',
            [
                EntityId::fromProviderNameAndId('example', 1),
                $transition,
                $context,
                true
            ]
        );
    }

    function it_fails_constructing_with_start_if_target_step_is_not_defined(
        Workflow $workflow,
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) : void {
        $transition->getWorkflow()->willReturn($workflow);
        $transition->getStepTo()->willReturn(null);

        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->getErrors()
            ->willReturn([]);

        $this->beConstructedThrough(
            'start',
            [
                EntityId::fromProviderNameAndId('example', 1),
                $transition,
                $context,
                true
            ]
        );

        $this->shouldThrow(FlowException::class)->duringInstantiation();
    }

    function it_transits_to_next_state(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) {
        $workflow
            ->getName()
            ->shouldNotBeCalled();

        $stepTo->getName()->willReturn(self::STEP_TO);
        $stepTo->getWorkflowName()->willReturn(self::WORKFLOW_NAME);

        $transition->getWorkflow()->willReturn($workflow);
        $transition->getStepTo()->willReturn($stepTo);

        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->toArray()
            ->willReturn([]);

        $this->transit($transition, $context, true)
            ->shouldBeAnInstanceOf(State::class);
    }

    function it_fallbacks_to_transition_workflow_name_if_step_doesnt_contain_the_name(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) {
        $workflow
            ->getName()
            ->shouldBeCalled()
            ->willReturn(self::WORKFLOW_NAME);

        $stepTo->getName()->willReturn(self::STEP_TO);
        $stepTo->getWorkflowName()->willReturn(null);

        $transition->getWorkflow()->willReturn($workflow);
        $transition->getStepTo()->willReturn($stepTo);

        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->toArray()
            ->willReturn([]);

        $this->transit($transition, $context, true)
            ->shouldBeAnInstanceOf(State::class);
    }

    function it_fails_to_transit_if_target_step_is_not_defined(
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties
    ) : void {
        $transition->getStepTo()->willReturn(null);

        $transition->getName()
            ->willReturn('transition');

        $context->getProperties()
            ->willReturn($properties);

        $properties->toArray()
            ->willReturn([]);

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $errorCollection->getErrors()
            ->willReturn([]);

        $this->shouldThrow(FlowException::class)
            ->during('transit', [$transition, $context, true]);
    }
}
