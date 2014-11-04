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

class BuildFormEvent extends Event
{
    const NAME = 'workflow.transition.handler.build-form';

    /**
     * @var Form
     */
    private $form;

    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var string
     */
    private $transitionName;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param Form     $form
     * @param Workflow $workflow
     * @param Item     $item
     * @param Context  $context
     * @param string   $transitionName
     */
    public function __construct(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        $this->workflow       = $workflow;
        $this->form           = $form;
        $this->item           = $item;
        $this->transitionName = $transitionName;
        $this->context        = $context;
    }

    /**
     * @return \Netzmacht\Workflow\Form\Form
     */
    public function getForm()
    {
        return $this->form;
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
     * @return Workflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }
}
