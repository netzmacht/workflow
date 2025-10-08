<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use DateTimeImmutable;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Exception\FlowException;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @psalm-import-type TErrorArray from ErrorCollection */
final class StateSpec extends ObjectBehavior
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

    /** @var TErrorArray */
    private static array $errors = [['error.message', [], null]];

    private EntityId $entityId;

    private Step $stepTo;

    public function let(DateTimeImmutable $dateTime, Workflow $workflow): void
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);
        $this->stepTo   = new Step(self::STEP_TO);

        $this->beConstructedWith(
            $this->entityId,
            self::WORKFLOW_NAME,
            self::TRANSITION_NAME,
            self::STEP_TO,
            true,
            self::$data,
            $dateTime,
            self::$errors,
            self::STATE_ID,
            self::TARGET_WORKFLOW_NAME,
        );

        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);
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
            self::$data,
            new DateTimeImmutable(),
            self::$errors,
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
            self::$data,
            new DateTimeImmutable(),
            self::$errors,
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
            self::$data,
            new DateTimeImmutable(),
            self::$errors,
            self::STATE_ID,
            self::TARGET_WORKFLOW_NAME,
        );

        $this->getWorkflowName()->shouldReturn(self::TARGET_WORKFLOW_NAME);
    }

    public function it_constructs_with_start(
        Workflow $workflow,
    ): void {
        $transition = new Transition('transition', $workflow->getWrappedObject(), $this->stepTo);

        $this->beConstructedThrough(
            'start',
            [
                EntityId::fromProviderNameAndId('example', 1),
                $transition,
                new Context(),
                true,
            ],
        );
    }

    public function it_fails_constructing_with_start_if_target_step_is_not_defined(
        Workflow $workflow,
    ): void {
        $transition = new Transition('transition', $workflow->getWrappedObject());

        $this->beConstructedThrough(
            'start',
            [
                EntityId::fromProviderNameAndId('example', 1),
                $transition,
                new Context(),
                true,
            ],
        );

        $this->shouldThrow(FlowException::class)->duringInstantiation();
    }

    public function it_transits_to_next_state(Workflow $workflow): void
    {
        $workflow
            ->getName()
            ->shouldNotBeCalled();

        $step = new Step(self::STEP_TO, workflowName: 'test');

        $transition = new Transition('transition', $workflow->getWrappedObject(), $step);

        $this->transit($transition, new Context(), true)
            ->shouldBeAnInstanceOf(State::class);
    }

    public function it_fallbacks_to_transition_workflow_name_if_step_doesnt_contain_the_name(
        Workflow $workflow,
    ): void {
        $workflow
            ->getName()
            ->shouldBeCalled()
            ->willReturn(self::WORKFLOW_NAME);

        $transition = new Transition('transition', $workflow->getWrappedObject(), $this->stepTo);

        $this->transit($transition, new Context(), true)
            ->shouldBeAnInstanceOf(State::class);
    }

    public function it_fails_to_transit_if_target_step_is_not_defined(): void
    {
        $workflow   = new Workflow('workflow', 'workflow');
        $transition = new Transition('transition', $workflow);

        $this->shouldThrow(FlowException::class)
            ->during('transit', [$transition, new Context(), true]);
    }
}
