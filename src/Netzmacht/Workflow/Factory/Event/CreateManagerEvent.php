<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Event\Factory;

use Netzmacht\Workflow\Manager;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateManagerEvent is dispatched when a workflow manager is created.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateManagerEvent extends Event
{
    const NAME = 'workflow.factory.create-repository-manager';

    /**
     * The created manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Workflow type.
     *
     * @var string
     */
    private $workflowType;

    /**
     * Name of the provider.
     * @var string
     */
    private $providerName;

    /**
     * Construct.
     *
     * @param string $providerName Provider name.
     * @param string $workflowType Workflow type.
     */
    public function __construct($providerName, $workflowType = null)
    {
        $this->workflowType = $workflowType;
        $this->providerName = $providerName;
    }

    /**
     * Get workflow type.
     *
     * @return string
     */
    public function getWorkflowType()
    {
        return $this->workflowType;
    }

    /**
     * Get provider name.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Get the manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set the created manager.
     *
     * @param Manager $manager The created manager.
     *
     * @return $this
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;

        return $this;
    }
}
