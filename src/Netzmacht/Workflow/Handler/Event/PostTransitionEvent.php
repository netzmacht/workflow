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
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PostTransitionEvent is dispatched after transition was executed.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class PostTransitionEvent extends Event
{
    const NAME = 'workflow.transition.handler.post-transition';

    /**
     * Workflow item state.
     *
     * @var State
     */
    private $state;

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
     * Transition context.
     *
     * @var Context
     */
    private $context;

    /**
     * Construct.
     *
     * @param Workflow $workflow Current workflow.
     * @param Item     $item     Workflow item.
     * @param Context  $context  Transition context.
     * @param State    $state    Workflow item state.
     */
    public function __construct(Workflow $workflow, Item $item, Context $context, State $state)
    {
        $this->state    = $state;
        $this->workflow = $workflow;
        $this->item     = $item;
        $this->context  = $context;
    }

    /**
     * Get workflow.
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
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
     * Get current state.
     *
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get workflow context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}
