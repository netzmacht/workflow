<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Form;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;

/**
 * Interface Form describes a form instance which is used for workflow transition.
 *
 * @package Netzmacht\Workflow\Form
 */
interface Form
{
    /**
     * Validate form data.
     *
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function validate(Context $context);

    /**
     * Render the form and return it as string.
     *
     * @return string
     */
    public function render();

    /**
     * Add label and description for a fieldset.
     *
     * @param string $name        Name of the fieldset.
     * @param string $label       Label of the fieldset.
     * @param string $description Description in the fieldset.
     * @param string $class       Optional css class.
     *
     * @return $this
     */
    public function setFieldsetDetails($name, $label, $description = null, $class = null);

    /**
     * Create a form field.
     *
     * @param string $name  Form field name.
     * @param string $type  Form field type.
     * @param array  $extra Form field extra configuration.
     *
     * @return FormField
     */
    public function createField($name, $type = 'text', array $extra = array());

    /**
     * Add a field to the form.
     *
     * @param FormField $formField Formfield to be added.
     * @param string    $fieldset  Name of the fieldset.
     *
     * @return $this
     */
    public function addField(FormField $formField, $fieldset = 'default');

    /**
     * Get form data of an validated form.
     *
     * @return array
     */
    public function getData();

    /**
     * Get errors of form.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection();
}
