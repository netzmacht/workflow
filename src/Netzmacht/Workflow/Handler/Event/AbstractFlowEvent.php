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
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractFlowEvent provides basic properties for a flow event.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
abstract class AbstractFlowEvent extends Event
{
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
     * @param Workflow $workflow The workflow.
     * @param Item     $item     The workflow item.
     * @param Context  $context  The transition context.
     */
    public function __construct(Workflow $workflow, Item $item, Context $context)
    {
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
     * Get workflow context.
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}
