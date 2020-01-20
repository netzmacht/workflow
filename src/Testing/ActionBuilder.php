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

use DateTimeImmutable;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\State;
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
     * Can be set to the name of a step that this action will transit to.
     * 
     * @var string
     */
    private $transitToStepWithName = null;

    /**
     * Can be set to the name of the transition that executes when transitToStepWithName is given.
     * 
     * @var string
     */
    private $transitionName = 'transitionDuringPostAction';

    function __construct() {
    }

    static function begin(): ActionBuilder {
        return new ActionBuilder();
    }

    function withTransitionName(string $transitionName): ActionBuilder
    {
        $this->transitionName = $transitionName;
        return $this;
    }

    function transitToNewStepWithName(string $stepname): ActionBuilder
    {
        $this->transitToStepWithName = $stepname;
        return $this;
    }

    function build():Action
    {
        $prophet = new Prophet;
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend('stdClass');
        $prophecy->willImplement('Netzmacht\Workflow\Flow\Action');

        if ($this->transitToStepWithName != null) {
            $builder = $this;
            $prophecy
                ->transit(Argument::any(), Argument::any(), Argument::any())
                ->will(function($args) use($builder) {
                    $transition = $args[0];
                    $item = $args[1];
                    $context = $args[2];
                    $newState = new State(
                        $item->getEntityId(),
                        $item->getWorkflowName(),
                        $builder->transitionName,
                        $builder->transitToStepWithName,
                        true, /* successful */
                        [], /* data */
                        new \DateTimeImmutable()
                    );

                    // ? $item->getLatestStateOccurred()->willReturn($newState);
                    return $newState;
                });
        }

        return $prophecy->reveal();
    }

}
