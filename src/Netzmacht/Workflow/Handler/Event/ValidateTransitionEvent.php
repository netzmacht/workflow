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
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;

/**
 * Class ValidateTransitionEvent is dispatched when validating a transition.
 *
 * Is is raised after the validation was made and contains the current validation state.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class ValidateTransitionEvent extends AbstractFlowEvent
{
    const NAME = 'workflow.transition.handler.validate';

    /**
     * The transition form.
     *
     * @var Form
     */
    private $form;

    /**
     * Validation state.
     *
     * @var bool
     */
    private $validated = true;

    /**
     * The transition name.
     *
     * @var string
     */
    private $transitionName;

    /**
     * Construct.
     *
     * @param Workflow $workflow       Current workflow.
     * @param string   $transitionName Transition name.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param Form     $form           Transition form.
     * @param bool     $validated      Validation state.
     */
    public function __construct(
        Workflow $workflow,
        $transitionName,
        Item $item,
        Context $context,
        Form $form,
        $validated
    ) {
        parent::__construct($workflow, $item, $context);

        $this->form           = $form;
        $this->transitionName = $transitionName;
        $this->validated      = $validated;
    }

    /**
     * Get the form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set validation state to invalid.
     *
     * @return $this
     */
    public function setInvalid()
    {
        $this->validated = false;

        return $this;
    }

    /**
     * Consider validation state.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->validated;
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
