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

/**
 * Class PostTransitionEvent is dispatched after transition was executed.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class PostTransitionEvent extends AbstractFlowEvent
{
    const NAME = 'workflow.transition.handler.post-transition';

    /**
     * Workflow item state.
     *
     * @var State
     */
    private $state;

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
        parent::__construct($workflow, $item, $context);

        $this->state    = $state;
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
}
