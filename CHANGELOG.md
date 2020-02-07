
Changelog
=========

[Unreleased]
------------

[2.1.0] (2020-02-07)

### Added

 - Add method execute to Transition
 - Item tracks state changes which can be released by Item#releaseRecordedStateChanges
 - Step contains the workflow name
 - State supports different workflow name for start and target states

### Changed

 - Transition can be initialized without a target step
 - Deprecate executeActions and executePostActions of Transition
 - Call Transition#execute in AbstractTransitionHandler
 - Use Item#releaseRecordedStateChanges in RepositoryBasedTransitionHandler to store all state changes
 - Transition#validate also validates post actions
 - Transition#getRequiredPayloadProperties also recognize options of post actions

### Fixed

 - Handle case that Transition#getStepTo is null

[2.0.2] (2019-12-05) 

### Fixed

 - Fix initial value of item state

[2.0.1] (2019-02-08)
--------------------

### Fixed

 - Fix available transitions if workflow has changed.
 - Fix item state history is not initialized properly which might cause an 
   `Parameter must be an array or an object that implements Countable` error.
 
### Added

 - `ActionFailedException` might contain action name and error collection now.
 - `ErrorCollection` implements `Countable` now
 - Added `Item#getLatestStateOccurred` and `Item#getLatestSuccessfulState`
 
### Changed

 - Actions will also fail if any error is added during transition.
 
### Deprecated

 - Deprecate `Item#getLatestState`. Use `Item#getLatestStateOccurred` or `Item#getLatestSuccessfulState` state instead.


[2.0.0] (2018-07-24)
------------------


[2.0.0-beta1] (2017-11-24)
------------------------

 - Allow to detach an item from a workflow.
 - Move error collection to the context.
 - Drop User and Role. Just support permissions.
 - Change user data handling: Introduce payload instead of handling forms.
 - Add required parameters to the constructor for flow elements.
 - Add strict types (PHP 7.1).
 - Switch to psr-4.
 - Utilize phpcq/all-tasks.


[Unreleased]:  https://github.com/netzmacht/workfow/compare/master...develop
[2.1.0]:       https://github.com/netzmacht/workfow/compare/2.0.2...2.1.0
[2.0.2]:       https://github.com/netzmacht/workfow/compare/2.0.1...2.0.2
[2.0.1]:       https://github.com/netzmacht/workfow/compare/2.0.0...2.0.1
[2.0.0]:       https://github.com/netzmacht/workfow/compare/2.0.0-beta1...2.0.0
[2.0.0-beta1]: https://github.com/netzmacht/workfow/compare/1.0.0-beta2...2.0.0-beta1
