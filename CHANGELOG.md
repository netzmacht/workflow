
Changelog
=========

[Unreleased]
-------------

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


[Unreleased]:  https://github.com/netzmacht/workfow/compare/2.0.0...hotfix/2.0.1
[2.0.0]:       https://github.com/netzmacht/workfow/compare/2.0.0-beta1...2.0.0
[2.0.0-beta1]: https://github.com/netzmacht/workfow/compare/1.0.0-beta2...2.0.0-beta1
