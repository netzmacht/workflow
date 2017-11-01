<?php

namespace spec\Netzmacht\Workflow\Handler\Listener;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Listener\EventDispatchingListener;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PreTransitionEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcher;

/**
 * Class TransitionHandlerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin EventDispatchingListener
 */
class NoOpListenerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';
    const CONTEXT_CLASS = 'Netzmacht\Workflow\Flow\Context';
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';
    const WORKFLOW_NAME = 'workflow_name';
    const STEP_NAME = 'step_name';

    protected static $entity = array('id' => 5);

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Listener\NoOpListener');
    }

    function it_is_a_dispatcher()
    {
        $this->shouldImplement('Netzmacht\Workflow\Handler\Listener');
    }

    function it_listens_on_build_form(
        Form $form,
        Workflow $workflow,
        Context $context,
        Item $item
    ) {
        $this->onBuildForm($form, $workflow, $item, $context, static::TRANSITION_NAME)->shouldBe(null);
    }

    function it_listens_on_validate(
        Form $form,
        Workflow $workflow,
        Context $context,
        Item $item
    ) {
        $this->onValidate($form, true, $workflow, $item, $context, static::TRANSITION_NAME)->shouldBeBool();
    }

    function it_bypasses_validation_state_on_listening_to_validate(
        Form $form,
        Workflow $workflow,
        Context $context,
        Item $item
    ) {
        $this
            ->onValidate($form, true, $workflow, $item, $context, static::TRANSITION_NAME)
            ->shouldBe(true);

        $this
            ->onValidate($form, false, $workflow, $item, $context, static::TRANSITION_NAME)
            ->shouldBe(false);
    }


    function it_listens_to_pre_transit(
        Workflow $workflow,
        Context $context,
        Item $item
    ) {
        $this->onPreTransit($workflow, $item, $context, static::TRANSITION_NAME)->shouldBe(null);
    }

    function it_listens_to_post_transit(
        Workflow $workflow,
        Context $context,
        Item $item,
        State $state
    ) {
        $this->onPostTransit($workflow, $item, $context, $state)->shouldBe(null);
    }
}
