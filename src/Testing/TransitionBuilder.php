<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     Erik Wegner <E_Wegner@web.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Testing;

use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Class TransitionBuilder allows building a customized MockTransition.
 *
 * @package Netzmacht\Workflow\Testing
 */
class TransitionBuilder
{
    /**
     * The configured transition.
     *
     * @var \Netzmacht\Workflow\Testing\MockTransition
     */
    private $mocktransition;

    function __construct(Transition $transition) {
        $this->mocktransition = new MockTransition($transition->getWorkflow(), $transition->getStepTo());
    }

    static function begin(Transition $transition): TransitionBuilder {
        return new TransitionBuilder($transition);
    }

    function isAllowed(): TransitionBuilder
    {
        $this->mocktransition->setAllowed(true);
        return $this;
    }

    function isNotAllowed(): TransitionBuilder
    {
        $this->mocktransition->setAllowed(false);
        return $this;
    }

    function build():MockTransition
    {
        return $this->mocktransition;
    }

    function withStepTo(Step $step): TransitionBuilder
    {
        $this->mocktransition->setStepTo($step);
        return $this;
    }
}
