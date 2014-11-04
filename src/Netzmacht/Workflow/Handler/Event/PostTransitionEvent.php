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

class PostTransitionEvent extends Event
{
    const NAME = 'workflow.transition.handler.post-transition';

    /**
     * @var State
     */
    private $state;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Item
     */
    private $item;

    /**
     * @param Workflow  $workflow
     * @param Item      $item
     * @param Context   $context
     * @param State     $state
     */
    public function __construct(Workflow $workflow, Item $item, Context $context, State $state)
    {
        $this->state    = $state;
        $this->workflow = $workflow;
        $this->item     = $item;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }
}
