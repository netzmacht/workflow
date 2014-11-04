<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler\Event;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildFormEvent is dispatched when transition form is built.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class BuildFormEvent extends Event
{
    const NAME = 'workflow.transition.handler.build-form';

    /**
     * Transition form.
     *
     * @var Form
     */
    private $form;

    /**
     * Current workflow.
     *
     * @var Workflow
     */
    private $workflow;

    /**
     * Workflow item.
     *
     * @var Item
     */
    private $item;

    /**
     * Name of current transition.
     *
     * @var string
     */
    private $transitionName;

    /**
     * Transition context.
     *
     * @var Context
     */
    private $context;

    /**
     * Construct.
     *
     * @param Form     $form           Transition form.
     * @param Workflow $workflow       Current workflow.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     */
    public function __construct(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        $this->workflow       = $workflow;
        $this->form           = $form;
        $this->item           = $item;
        $this->transitionName = $transitionName;
        $this->context        = $context;
    }

    /**
     * Get transition form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get workflow item.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get transition name.
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * Get current workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Get transition context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}
