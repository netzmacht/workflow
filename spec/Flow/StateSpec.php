<?php

declare(strict_types=1);

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

/** @psalm-import-type TErrorArray from ErrorCollection */
class StateSpec extends ObjectBehavior
{
    private const string WORKFLOW_NAME        = 'workflow_name';
    private const string TARGET_WORKFLOW_NAME = 'workflow_name_target';
    private const string TRANSITION_NAME      = 'transition_name';
    private const string STEP_TO              = 'step_to';
    private const int STATE_ID                = 121;

    /** @var array<string, mixed> */
    private static array $data = [
        'foo' => true,
        'bar' => false,
    ];

    private EntityId $entityId;

    /** @var TErrorArray */
    private static array $errors = [['error.message', [], null]];

    public function let(DateTimeImmutable $dateTime): void
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);

        $this->beConstructedWith(
            $this->entityId,
            self::WORKFLOW_NAME,
            self::TRANSITION_NAME,
            self::STEP_TO,
            true,
            static::$data,
            $dateTime,
            static::$errors,
            self::STATE_ID,
            self::TARGET_WORKFLOW_NAME,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(State::class);
    }

    public function it_knows_current_step(): void
    {
        $this->getStepName()->shouldReturn(self::STEP_TO);
    }

    public function it_knows_last_transition(): void
    {
        $this->getTransitionName()->shouldReturn(self::TRANSITION_NAME);
    }

    public function it_knows_reached_time(): void
    {
        $this->getReachedAt()->shouldBeAnInstanceOf(DateTimeImmutable::class);
    }

    public function it_stores_data(): void
    {
        $this->getData()->shouldReturn(static::$data);
    }

    public function it_knows_entity_id(): void
    {
        $this->getEntityId()->shouldReturn($this->entityId);
    }

    public function it_stores_error(): void
    {
        $this->getErrors()->shouldReturn(static::$errors);
    }

    public function it_has_an_id(): void
    {
        $this->getStateId()->shouldReturn(self::STATE_ID);
    }

    public function it_knows_start_workflow_name(): void
    {
        $this->getStartWorkflowName()->shouldReturn(self::WORKFLOW_NAME);
    }

    public function it_knows_target_workflow_name(): void
    {
        $this->getTargetWorkflowName()->shouldReturn(self::TARGET_WORKFLOW_NAME);
    }

    public function it_knows_workflow_name(): void
    {
        $this->getWorkflowName()->shouldReturn(self::TARGET_WORKFLOW_NAME);
    }

    public function it_uses_start_workflow_as_target_workflow_if_target_workflow_is_not_set(): void
    {
        $this->beConstructedWith(
            $this->entityId,
            self::WORKFLOW_NAME,
            self::TRANSITION_NAME,
            self::STEP_TO,
            true,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            self::STATE_ID,
        );

        $this->getTargetWorkflowName()->shouldReturn(self::WORKFLOW_NAME);
    }

    public function it_uses_start_workflow_as_current_workflow_for_not_successful_states(): void
    {
        $this->beConstructedWith(
            $this->entityId,
            self::WORKFLOW_NAME,
            self::TRANSITION_NAME,
            self::STEP_TO,
            false,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            self::STATE_ID,
            self::TARGET_WORKFLOW_NAME,
        );

        $this->getWorkflowName()->shouldReturn(self::WORKFLOW_NAME);
    }

    public function it_uses_target_workflow_as_current_workflow_for_successful_states(): void
    {
        $this->beConstructedWith(
            $this->entityId,
            self::WORKFLOW_NAME,
            self::TRANSITION_NAME,
            self::STEP_TO,
            true,
            static::$data,
            new DateTimeImmutable(),
            static::$errors,
            self::STATE_ID,
            self::TARGET_WORKFLOW_NAME,
        );

        $this->getWorkflowName()->shouldReturn(self::TARGET_WORKFLOW_NAME);
    }

    public function it_constructs_with_start(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties,
    ): void {
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
                true,
            ],
        );
    }

    public function it_fails_constructing_with_start_if_target_step_is_not_defined(
        Workflow $workflow,
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties,
    ): void {
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
                true,
            ],
        );

        $this->shouldThrow(FlowException::class)->duringInstantiation();
    }

    public function it_transits_to_next_state(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties,
    ): void {
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

    public function it_fallbacks_to_transition_workflow_name_if_step_doesnt_contain_the_name(
        Workflow $workflow,
        Transition $transition,
        Step $stepTo,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties,
    ): void {
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

    public function it_fails_to_transit_if_target_step_is_not_defined(
        Transition $transition,
        Context $context,
        ErrorCollection $errorCollection,
        Properties $properties,
    ): void {
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
