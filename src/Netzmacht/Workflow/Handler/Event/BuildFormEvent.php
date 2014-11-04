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
 * Class BuildFormEvent is dispatched when transition form is built.
 *
 * @package Netzmacht\Workflow\Handler\Event
 */
class BuildFormEvent extends AbstractFlowEvent
{
    const NAME = 'workflow.transition.handler.build-form';

    /**
     * Transition form.
     *
     * @var Form
     */
    private $form;

    /**
     * Name of current transition.
     *
     * @var string
     */
    private $transitionName;

    /**
     * Construct.
     *
     * @param Form     $form           Transition form.
     * @param Workflow $workflow       Current workflow.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     */
    public function __construct(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        parent::__construct($workflow, $item, $context);

        $this->form           = $form;
        $this->transitionName = $transitionName;
    }

    /**
     * Get transition form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get transition name.
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }
}
