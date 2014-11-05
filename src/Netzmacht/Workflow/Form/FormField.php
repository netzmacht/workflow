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

/**
 * Interface FormField describes an form field which can be added to transition form.
 *
 * This interface is an reduced description of what contao-community-alliance/dc-general PropertyInterface describes.
 *
 * @package Netzmacht\Workflow\Form
 */
interface FormField
{
    /**
     * Return the name of the form field.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the label language key.
     *
     * @param string $value The label value.
     *
     * @return FormField
     */
    public function setLabel($value);

    /**
     * Return the label of the form field.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set the description language key.
     *
     * @param string $value The description text.
     *
     * @return FormField
     */
    public function setDescription($value);
    
    /**
     * Return the description of the form field.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set the default value of the form field.
     *
     * @param mixed $value The default value.
     *
     * @return FormField
     */
    public function setDefaultValue($value);
    
    /**
     * Return the default value of the form field.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Set the widget type name.
     *
     * @param string $value The type name of the widget.
     *
     * @return FormField
     */
    public function setWidgetType($value);

    /**
     * Return the widget type name.
     *
     * @return string
     */
    public function getWidgetType();

    /**
     * Set the valid values of this form field.
     *
     * @param array $value The options.
     *
     * @return FormField
     */
    public function setOptions($value);

    /**
     * Return the valid values of this form field.
     *
     * @return array|null
     */
    public function getOptions();

    /**
     * Set the explanation language string.
     *
     * @param string $value The explanation text.
     *
     * @return FormField
     */
    public function setExplanation($value);

    /**
     * Return the explanation of the form field.
     *
     * @return string
     */
    public function getExplanation();

    /**
     * Set the extra data of the form field.
     *
     * @param array $value The extra data for this form field.
     *
     * @return FormField
     */
    public function setExtra($value);

    /**
     * Fetch the extra data of the form field.
     *
     * @return array
     */
    public function getExtra();

    /**
     * Add field to a form.
     *
     * @param Form   $form     Form object.
     * @param string $fieldset Name of the fieldset.
     *
     * @return $this
     */
    public function addToForm(Form $form, $fieldset = 'default');
}
