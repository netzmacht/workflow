<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
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
     * Construct.
     *
     * @param string $type Form type.
     */
    public function __construct($type)
    {
        $this->type = $type;
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
}
