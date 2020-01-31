<?php

/**
 * workflow.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Condition\Transition\PayloadPropertyCondition;
use Netzmacht\Workflow\Flow\Condition\Workflow\ProviderNameCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\AbstractTransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
use Netzmacht\Workflow\Manager\WorkflowManager;
use Netzmacht\Workflow\Util\Comparison;

/*
 * First let's create the workflow.
 */

$workflow = new Workflow('test', 'example');

// Our workflow should only handle the example data provider.
$workflow->addCondition(new ProviderNameCondition('example'));

/*
 * Now define and configure the steps.
 */
$createdStep = new Step('created');
$editedStep  = new Step('edited');

// Deleted is the last step, define it.
$deletedStep = new Step('deleted');
$deletedStep->setFinal(true);

$workflow
    ->addStep($createdStep)
    ->addStep($editedStep)
    ->addStep($deletedStep);

/*
 * Create the process by defining transitions.
 */

// Every transition transits to a defined step. Here $createdStep
$createTransition = new Transition('create', $workflow, $createdStep);
$editTransition   = new Transition('edit', $workflow, $editedStep);
$deleteTransition = new Transition('deleted', $workflow, $deletedStep);

// Now let's define which transition is available after a step
$createdStep
    ->allowTransition($editTransition)
    ->allowTransition($deleteTransition);

// Circular transitions are allowed (edit -> edited -> edit)
$editedStep
    ->allowTransition($editTransition)
    ->allowTransition($deleteTransition);

// The workflow has get a start transition. This transition has be called at first.
$workflow->setStartTransition($createTransition);

/*
 * Our workflow is defined. One thing, it's missing. We want to require that the delete transition is confirmed.
 * Let's create a condition for it.
 */

$deleteTransition->addCondition(new PayloadPropertyCondition('confirm', true, Comparison::IDENTICAL));

/*
 * In a real workflow you want to define actions which are handled by each transition.
 *
 * Let's create an action which sets the current step as state to the example.
 */

$action = new class implements \Netzmacht\Workflow\Flow\Action {
    public function getRequiredPayloadProperties(Item $item): array
    {
        return [];
    }

    public function validate(Item $item, Context $context): bool
    {
        return true;
    }

    public function transit(Transition $transition, Item $item, Context $context): void
    {
        // Assume that the entity is an array object. Workflow doesn't care which format the entity has.
        // But your action have to be aware.
        $entity = $item->getEntity();

        $entity['state'] = $transition->getStepTo()->getName();
    }
};

$createTransition->addAction($action);
$editTransition->addAction($action);
$deleteTransition->addAction($action);

/*
 * The workflow is defined. To handle the workflow we register the workflow to the workflow manager.
 */

// As workflow doesn't care about your data, you have to handle it.  For the simplify whe create a noop state repository
// and a stupid transition handler.

$stateRepository = new class implements StateRepository {
    public function find(EntityId $entityId): iterable
    {
        // We have to fetch all state in an ascending order here
        return [];
    }

    public function add(State $state): void
    {
        // Add a new state
    }
};

$transitionHandler = new class($stateRepository) extends AbstractTransitionHandler {
    public function transit(): State
    {
        $state = $this->executeTransition();

        // We actually have to store the state in a state repository now.
        // We could store the item in an repository as well now.

        return $state;
    }
};

$transitionHandlerFactory = new class($transitionHandler) implements TransitionHandlerFactory {
    /**
     * @var TransitionHandler
     */
    private $transitionHandler;

    public function __construct(TransitionHandler $transitionHandler)
    {
        $this->transitionHandler = $transitionHandler;
    }

    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        ?string $transitionName,
        string $providerName,
        StateRepository $stateRepository
    ): TransitionHandler {
        return $this->transitionHandler;
    }
};

// Now everything is prepared. The workflow manager decides which workflow to use.
$manager = new WorkflowManager($transitionHandlerFactory, $stateRepository, [$workflow]);

/*
 * Handle an item.
 */

$entityId = EntityId::fromProviderNameAndId('example', 1);
$entity   = new ArrayObject(['state' => null]);

// Create a workflow item, which knows it current state
$item    = $manager->createItem($entityId, $entity);
$handler = $manager->handle($item, 'start');

// If workflow which supports the entity is found, null is returned.
if ($handler) {
    $payload = [];

    // if some actions require some payload, you get the required properties here.
    if ($handler->getRequiredPayloadProperties()) {
        // We don't have such actions. Just leave it empty
    }

    // We have to validate the handler first. All conditions are checked.
    if ($handler->validate($payload)) {
        // Finally let's transit to the next state.
        $state = $handler->transit();
    } else {
        $errors = $handler->getContext()->getErrorCollection();
        // Display the errors.
    }
}
