<?php

/**
 * @package    workflow
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

/**
 * Interface TransitionHandlerFactory describes factory for the workflow transition handler.
 *
 * @package Netzmacht\Workflow\Factory
 */
interface TransitionHandlerFactory
{
    /**
     * Create a transition handler.
     *
     * @param Item            $item            Workflow item.
     * @param Workflow        $workflow        Workflow definition.
     * @param string          $transitionName  Transition name.
     * @param string          $providerName    Provider name.
     * @param StateRepository $stateRepository The state repository.
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
