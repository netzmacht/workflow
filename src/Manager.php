<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

namespace Netzmacht\Workflow;

use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Factory\TransitionHandlerFactory;
use Netzmacht\Workflow\Manager\WorkflowManager;

/**
 * Backward compatibility workflow manager class.
 *
 * @package Netzmacht\Workflow
 */
class Manager extends WorkflowManager
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        $workflows = array()
    ) {
        parent::__construct($handlerFactory, $stateRepository, $workflows);

        $message  = '"Netzmacht\Workflow\Manager"" is deprecated and will be removed in v1.0.0 stable.';
        $message .= ' Use "Netzmacht\Workflow\Manager\WorkflowManager" instead.';

        trigger_error($message, E_USER_DEPRECATED);
    }
}
