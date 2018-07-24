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

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;
use PhpSpec\ObjectBehavior;

/**
 * Class ContextSpec
 *
 * @package spec\Netzmacht\Contao\Workflow\Flow
 */
class ContextSpec extends ObjectBehavior
{
    const CUSTOM_NS = 'custom';

    function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    function it_accepts_initial_properties(Properties $properties)
    {
        $this->beConstructedWith($properties);

        $this->getProperties()->shouldBe($properties);
    }

    function it_accepts_initial_payload(Properties $payload)
    {
        $this->beConstructedWith(null, $payload);

        $this->getPayload()->shouldBe($payload);
    }

    function it_has_properties()
    {
        $this->getProperties()->shouldHaveType(Properties::class);
    }

    function it_has_payload()
    {
        $this->getPayload()->shouldHaveType(Properties::class);
    }

    function it_has_error_collection()
    {
        $this->getErrorCollection()->shouldHaveType(ErrorCollection::class);
    }
}
