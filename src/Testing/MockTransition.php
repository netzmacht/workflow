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

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\Exception\Example\FailureException;

/**
 * Class MockTransition is for testing purposes.
 *
 * @package Netzmacht\Workflow\Testing
 */
class MockTransition extends Transition
{
    private $isAllowed = false;
    private $isAllowedCallCount = 0;
    private $stepTo = null;

    function __construct($workflow, $stepTo)
    {
        parent::__construct("MockTransition", $workflow, $stepTo);
    }

    public function setAllowed($isAllowed = true)
    {
        $this->isAllowed = $isAllowed;
    }

    public function isAllowed(Item $item, Context $context): bool
    {
        $this->isAllowedCallCount++;
        return $this->isAllowed;
    }

    public function isAllowedShouldHaveBeenCalledTimes(int $count)
    {
        if ($this->isAllowedCallCount != $count) {
            throw new FailureException(sprintf(
                "isAllowed expected %i calls, acutal number of calls is %i",
                $count,$this->isAllowedCallCount));
        }
    }

    function setStepTo(Step $step)
    {
        $this->stepTo = $step;
    }

    function getStepTo(): ?Step
    {
        return $this->stepTo;
    }
}
