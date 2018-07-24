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

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
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
     * @param string|null        $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     *
     * @throws WorkflowException If invalid transition name is given.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        string $transitionName = null,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler
    ) {
        parent::__construct($item, $workflow, $transitionName, $transactionHandler);
        
        $this->entityRepository = $entityRepository;
        $this->stateRepository  = $stateRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception If something went wrong during action execution.
     */
    public function transit(): State
    {
        $this->guardValidated();

        $this->transactionHandler->begin();

        try {
            $state = $this->executeTransition();

            $this->stateRepository->add($state);
            $this->entityRepository->add($this->getItem()->getEntity());
        } catch (\Exception $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        return $state;
    }
}
