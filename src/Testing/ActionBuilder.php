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

use Netzmacht\Workflow\Flow\Action;
use Prophecy\Argument;
use Prophecy\Prophet;

/**
 * Class ActionBuilder allows building a customized MockAction.
 *
 * @package Netzmacht\Workflow\Testing
 */
class ActionBuilder
{
    /**
     * Contains the callback function for the action's transit function.
     * 
     * @var \Closure The callback
     */
    private $transitClosure = null;

    private function __construct() {
    }

    static function begin(): ActionBuilder {
        return new ActionBuilder();
    }

    function onTransit(\Closure $closure): ActionBuilder
    {
        $this->transitClosure = $closure;
        return $this;
    }

    function build():Action
    {
        $prophet = new Prophet;
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend('stdClass');
        $prophecy->willImplement('Netzmacht\Workflow\Flow\Action');

        if ($this->transitClosure != null) {
            $builder = $this;
            $prophecy
                ->transit(Argument::any(), Argument::any(), Argument::any())
                ->will(function($args) use($builder) {
                    $transition = $args[0];
                    $item = $args[1];
                    $context = $args[2];
                    ($builder->transitClosure)($transition, $item, $context);
                });
        }

        return $prophecy->reveal();
    }

}
