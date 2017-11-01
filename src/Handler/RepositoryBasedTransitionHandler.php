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

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class RepositoryBasedTransitionHandler handles the transition to another step in the workflow.
 *
 * It uses an collection repository approach to store entities.
 *
 * @package Netzmacht\Workflow
 */
class RepositoryBasedTransitionHandler extends AbstractTransitionHandler
{
    /**
     * The entity repository.
     *
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * The state repository.
     *
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string             $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     * @param Listener           $listener           Transition handler dispatcher.
     *
     * @throws WorkflowException If invalid transition name is given.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Listener $listener
    ) {
        parent::__construct($item, $workflow, $transitionName, $transactionHandler, $listener);
        
        $this->entityRepository = $entityRepository;
        $this->stateRepository  = $stateRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception If something went wrong during action execution.
     */
    public function transit()
    {
        $this->guardValidated();

        $this->transactionHandler->begin();

        try {
            $this->listener->onPreTransit(
                $this->getWorkflow(),
                $this->getItem(),
                $this->getContext(),
                $this->getTransition()->getName()
            );

            $state = $this->executeTransition();

            $this->stateRepository->add($state);
            $this->entityRepository->add($this->getItem()->getEntity());
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        $this->listener->onPostTransit($this->getWorkflow(), $this->getItem(), $this->getContext(), $state);

        return $state;
    }
}
