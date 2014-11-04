<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;

/**
 * Class TransitionHandler handles the transition to another step in the workflow.
 *
 * @package Netzmacht\Workflow
 */
interface TransitionHandler
{
    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow();

    /**
     * Get the item.
     *
     * @return Item
     */
    public function getItem();

    /**
     * Get the input form.
     *
     * @return Form
     */
    public function getForm();

    /**
     * Get the transition.
     *
     * @return Transition
     *
     * @throws TransitionNotFoundException If transition was not found.
     */
    public function getTransition();

    /**
     * Get current step. Will return null if workflow is not started yet.
     *
     * @return \Netzmacht\Workflow\Flow\Step|null
     */
    public function getCurrentStep();

    /**
     * Consider if it handles a start transition.
     *
     * @return bool
     */
    public function isWorkflowStarted();

    /**
     * Consider if input is required.
     *
     * @return bool
     */
    public function requiresInputData();

    /**
     * Get the context.
     *
     * @return Context
     */
    public function getContext();

    /**
     * Validate the input.
     *
     * @param \Netzmacht\Workflow\Form\Form $form The transition form instance.
     *
     * @return bool
     */
    public function validate(Form $form);

    /**
     * Transit to next step.
     *
     * @throws WorkflowException For a workflow specific error.
     * @throws \Exception        For any error caused maybe by 3rd party code in the actions.
     *
     * @return State
     */
    public function transit();
}
