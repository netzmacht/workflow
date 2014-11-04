<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Factory;


use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;

interface TransitionHandlerFactory
{
    /**
     * Create a transition handler.
     *
     * @param Item            $item
     * @param Workflow        $workflow
     * @param string          $transitionName
     * @param string          $providerName
     * @param StateRepository $stateRepository
     *
     * @return TransitionHandler
     */
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        $transitionName,
        $providerName,
        StateRepository $stateRepository
    );
}
