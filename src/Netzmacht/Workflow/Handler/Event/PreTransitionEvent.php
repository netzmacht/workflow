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

/**
 * Class PreTransitionEvent is dispatched before transition take starts.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class PreTransitionEvent extends AbstractFlowEvent
{
    const NAME = 'workflow.transition.handler.pre-transition';

    /**
     * Transition name.
     *
     * @var string
     */
    private $transitionName;

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
        parent::__construct($workflow, $item, $context);

        $this->transitionName = $transitionName;
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

}
