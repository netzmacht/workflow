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

class PreTransitionEvent extends Event
{
    const NAME = 'workflow.transition.handler.pre-transition';

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var string
     */
    private $transitionName;
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Workflow $workflow
     * @param Item     $item
     * @param Context  $context
     * @param          $transitionName
     */
    public function __construct(Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        $this->workflow       = $workflow;
        $this->item           = $item;
        $this->transitionName = $transitionName;
        $this->context = $context;
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
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    public function getContext()
    {
        return $this->context;
    }
}
