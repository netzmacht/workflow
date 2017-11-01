
Framework independent workflow library
======================================

[![Build Status](http://img.shields.io/travis/netzmacht/workflow/master.svg?style=flat-square)](https://travis-ci.org/netzmacht/workflow)
[![Version](http://img.shields.io/packagist/v/netzmacht/workflow.svg?style=flat-square)](http://packagist.com/packages/netzmacht/workflow)
[![Code quality](http://img.shields.io/scrutinizer/g/netzmacht/workflow.svg?style=flat-square)](https://scrutinizer-ci.com/g/netzmacht/workflow/)
[![Code coverage](http://img.shields.io/scrutinizer/coverage/g/netzmacht/workflow.svg?style=flat-square)](https://scrutinizer-ci.com/g/netzmacht/workflow/)

This is a framework independent workflow library. It provides an step-transition based workflow implementation for 
processing entities through its life cycle.

Due to its framework independency it does not run as a standalone workflow library. The entity/data implementation and
 input processing via forms have to be implemented.
 

Features
--------

**The main concept**
 * An entity processes different steps in its lifecycle. 
 * The process between two steps is called Transition.
 * A transition can depend on conditions which determine if the transition is available.
 * Each transition contains a list of actions which are performed to reach the next step.
 * Actions can require additional user input to perform the transition.
 * User input are handled by a form.
 
**Workflow items**
 * The Item wraps the entity to provide workflow related informations. 
 * It knows the current state and the whole state history.
 * Due to the flexibility of the data structure the EntityId is used to indentify an entity.
 
**Worfklow**
 * An workflow is defined for a specific entities form a specific data provider.
 * The workflow is the definition of multiple steps and their transitions.
 * A workflow always has one start transition.
 * It can have multiple end transitions.

**Manager**
 * There can be multiple workflow definitions for the same data provider.
 * The manager selects the matching workflow and creates the transition handler.
 * At the moment an item can only be in one workflow.
 
**More features**
 * Collection based repositories.
 * Transaction save transitions.
 * Flexible config system for workflows, steps and transitions.
 
Requirements
------------

This library requires at least PHP 7.1.

Credits
-------

This library is heavenly inspired by the great workflow implementation of [orocrm plattform](http://github.com/orocrm/plattform)
and got some concepts from the [LexikWorkflowBundle](https://github.com/lexik/LexikWorkflowBundle).
