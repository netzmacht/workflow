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
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PreTransitionEvent is dispatched before transition take starts.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class PreTransitionEvent extends Event
{
    const NAME = 'workflow.transition.handler.pre-transition';

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
     * Transition name.
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
     * @param Workflow $workflow       Current workflow.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     */
    public function __construct(Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        $this->workflow       = $workflow;
        $this->item           = $item;
        $this->transitionName = $transitionName;
        $this->context        = $context;
    }

    /**
     * Get the workflow.
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
     * Get name of current transition.
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
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
