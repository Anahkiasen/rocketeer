### Changelog

### 0.x.x (roadmap)

- Ability to select which severs a Task executes on, on a per-task basis
- Add compatibility to Laravel 4.0

### 0.5.0 (stable)

- **Add a `deploy:update` task that updates the remote server without doing a new release**
- **Add a `deploy:test` to run the tests on the server**
- **Rocketeer can now prompt for Git credentials if you don't want to store them in the config**
- The `deploy:check` command now checks PHP extensions for the cache/database/session drivers you set
- Rocketeer now share logs by default between releases
- Add ability to specify an array of Tasks in Rocketeer::before|after
- Added a `$silent` flag to make a `Task::run` call silent no matter what
- Rocketeer now displays how long the task took

### 0.4.0

- **Add ability to share files and folders between releases**
- **Add ability to create custom tasks integrated in the CLI**
- **Add a `deploy:check` Task that checks if the server is ready to receive a Laravel app**
- Add `Task::listContents` and `Task::fileExists` helpers
- Add Task helper to run outstanding migrations
- Add `Rocketeer::add` method on the facade to register custom Tasks
- Fix `Task::runComposer` not taking into account a local `composer.phar`

### 0.3.2

- Fixed wrong tag used in `deploy:cleanup`

### 0.3.1

- Added `--pretend` flag on all commands to print out a list of the commands that would have been executed instead of running them

### 0.3.0

- Added `Task::runInFolder` to run tasks in a specific folder
- Added `Task::runForCurrentRelease` Task helper
- Fixed a bug where `Task::run` would only return the last line of the command's output
- Added `Task::runTests` methods to run the PHPUnit tests of the application
- Integrated `Task::runTests` in the `Deploy` task under the `--tests` flag ; failing tests will cancel deploy and rollback

### 0.2.0

- The core of Rocketeer's actions is now split into a system of Tasks for flexibility
- Added a `Rocketeer` facade to easily add tasks via `before` and `after` (see Tasks docs)

### 0.1.1

- Fixed a bug where the commands would try to connect to the remote hosts on construct
- Fixed `ReleasesManager::getPreviousRelease` returning the wrong release

### 0.1.0

- Add `deploy:teardown` to remove the application from remote servers
- Add support for the connections defined in the remote config file
- Add `deploy:rollback` and `deploy:current` commands
- Add `deploy:cleanup` command
- Add config file
- Add `deploy:setup` and `deploy:deploy` commands
