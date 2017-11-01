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

namespace Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Form\Form;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateFormEvent is dispatched when creating a new form instance.
 *
 * @package Netzmacht\Workflow\Event\Factory
 */
class CreateFormEvent extends Event
{
    const NAME = 'workflow.factory.create-form';

    /**
     * The form instance.
     *
     * @var Form
     */
    private $form;

    /**
     * The form type.
     *
     * @var string
     */
    private $type;

    /**
     * The form name.
     *
     * @var string
     */
    private $name;

    /**
     * Construct.
     *
     * @param string $type Form type.
     * @param string $name Form name.
     */
    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Get the form instance.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set the form instance.
     *
     * @param Form $form Form instance.
     *
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get the form type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get form name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
