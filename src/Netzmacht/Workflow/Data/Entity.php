<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Data;

use Traversable;

/**
 * Interface Entity describes the public interface of an data entity being processed in a workflow.
 *
 * @package Netzmacht\Workflow\Data
 */
interface Entity
{
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator();

    /**
     * Copy this model, without the id.
     *
     * @return void
     */
    public function __clone();

    /**
     * Get the entity id.
     *
     * @return EntityId
     */
    public function getEntityId();

    /**
     * Fetch the property with the given name from the model.
     *
     * This method returns null if an unknown property is retrieved.
     *
     * @param string $strPropertyName The property name to be retrieved.
     *
     * @return mixed The value of the given property.
     */
    public function getProperty($strPropertyName);

    /**
     * Fetch all properties from the model as an name => value array.
     *
     * @return array
     */
    public function getPropertiesAsArray();

    /**
     * Fetch meta information from model.
     *
     * @param string $strMetaName The meta information to retrieve.
     *
     * @return mixed The set meta information or null if undefined.
     */
    public function getMeta($strMetaName);

    /**
     * Update the property value in the model.
     *
     * @param string $strPropertyName The property name to be set.
     *
     * @param mixed  $varValue        The value to be set.
     *
     * @return void
     */
    public function setProperty($strPropertyName, $varValue);

    /**
     * Update all properties in the model.
     *
     * @param array $arrProperties The property values as name => value pairs.
     *
     * @return void
     */
    public function setPropertiesAsArray($arrProperties);

    /**
     * Update meta information in the model.
     *
     * @param string $strMetaName The meta information name.
     *
     * @param mixed  $varValue    The meta information value to store.
     *
     * @return void
     */
    public function setMeta($strMetaName, $varValue);

    /**
     * Check if this model have any properties.
     *
     * @return boolean true if any property has been stored, false otherwise.
     */
    public function hasProperties();
}
