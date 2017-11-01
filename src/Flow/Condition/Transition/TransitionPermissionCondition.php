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

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class TransitionPermissionCondition limit permission of a transition to the given transition roles.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class TransitionPermissionCondition extends AbstractPermissionCondition
{
    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
        $permission = $transition->getPermission();
        if ($this->checkPermission($permission)) {
            return true;
        }

        $errorCollection->addError(
            'transition.condition.transition-permission',
            array((string) $permission)
        );

        return false;
    }
}
