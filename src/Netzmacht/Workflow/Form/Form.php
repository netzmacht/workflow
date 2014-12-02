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
use Netzmacht\Workflow\Flow\Item;

/**
 * Interface Form describes a form instance which is used for workflow transition.
 *
 * @package Netzmacht\Workflow\Form
 */
interface Form
{
    /**
     * Validate form data and set form values as context params.
     *
     * @param Item    $item    Current workflow item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function validate(Item $item, Context $context);

    /**
     * Get errors of form.
     *
     * @return ErrorCollection
     */
    public function getErrorCollection();
}
