<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface TransitionHandlerFactory describes the factory for the workflow transition handler.
 */
interface TransitionHandlerFactory
{
    /**
     * Create a transition handler.
     *
     * @param Item            $item            Workflow item.
     * @param Workflow        $workflow        Workflow definition.
     * @param string|null     $transitionName  Transition name.
     * @param string          $providerName    Provider name.
     * @param StateRepository $stateRepository The state repository.
     */
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        string|null $transitionName,
        string $providerName,
        StateRepository $stateRepository,
    ): TransitionHandler;
}
