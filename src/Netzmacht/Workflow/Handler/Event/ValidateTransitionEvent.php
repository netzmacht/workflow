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
use Symfony\Component\EventDispatcher\Event;

class ValidateTransitionEvent extends Event
{
    const NAME = 'workflow.transition.handler.validate';

    /**
     * @var Form
     */
    private $form;

    /**
     * @var bool
     */
    private $validated = true;
    /**
     * @var Workflow
     */
    private $workflow;
    /**
     * @var string
     */
    private $transitionName;
    /**
     * @var Item
     */
    private $item;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param Workflow $workflow
     * @param string   $transitionName
     * @param Context  $context
     * @param Item     $item
     * @param Form     $form
     * @param bool     $validated
     */
    public function __construct(Workflow $workflow, $transitionName, Item $item, Context $context, Form $form, $validated)
    {
        $this->form = $form;
        $this->workflow = $workflow;
        $this->transitionName = $transitionName;
        $this->item = $item;
        $this->context = $context;
        $this->validated = $validated;
    }

    /**
     * @return \Netzmacht\Workflow\Form\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    public function setInvalid()
    {
        $this->validated = false;

        return $this;
    }

    public function isValid()
    {
        return $this->validated;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}
