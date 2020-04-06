# laravel-craftsman Changelog

## Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.9.0] - 2020-03-08

### Added

-   Added `getNamespace` method to `CraftsmanFileSytem.php`
-   Added `--invokable` option to `craft:controller`
-   Added `craft:rule` command
-   Added `craft:provider` command
-   Refactored tests to check for custom template path for each command

### Changed

-   Refactored `craft:model` command to use --controller|-c instead of --collection|-c
-   Refactored all `Commands/Craft*.php` to support `getNamespace` method
-   Refactored all `Commands/Craft*.php` to include new `--debug` option
-   Refactored `app/Exception/Handler` to fixed `Undefined Offset`
-   Added test to support `--controller` in CraftModelTest
-   Added more tests to `CraftListenerTest`
-   Added more tests to `CraftEveTest`

### Fixed

- Fixed `craft:command` template did not properly extend Command
- Fixed issues when running on PHP 7.4
    * implode parameter alignments
    * Upgraded mustache package to 2.13 (issues with 2.12 and missing variables)

## [1.8.0] - 2020-02-02

### Added

-   Added `craft:command` command
-   Added `craft:event` and `craft:listener` commands

## [1.7.2] - 2019-09-25

### Added

-   Added support for crafting migrations based on `--foreign` short format
    -   `laravel-craftsman craft:migration create_members_table --foreign=member_id` will populate `ftable` and `fkey`
        ```php
        $table->foreign('member_id')->references('id')->on('members');
        ```
-   Added support for overriding defaults using published configuration file
    -   `laravel-craftsman publish`

## [1.7.0] - 2019-09-26

### Added

-   Added `craft:api` command
-   Code cleanup (based on phpinsights analysis)

## [1.6.3] - 2019-08-28

### Fixed

-   Fixed `craft:controller` to craft new controller in `App/Http/Controllers` directory when using `resource` flag
-   Fixed test case to `CraftControllerTest::should_create_resource_controller` to cover correct resource creation related to `resource` flag (see above)

### Modified

-   Modified `craft:controller` shortcut for `resource` controller from `-u` to `-r`
-   Modified npm tasks for running test, adding `test:all` task
-   Modified `task:stress` to call `stress-test.sh` task so it will properly handle stress retries

## [1.6.2] - 2019-08-14

### Added

-   `craft` command alias for `interactive`
    -   `$ laravel-craftsman craft`

## [1.6.1] - 2019-08-03

### Added

-   feature: add `foreign` constraint support when crafting migrations
-   feature: add `current` option when crafting migrations
-   feature: add `migration` option when crafting models
-   admin: updated tests
-   admin: updated readme
-

## [1.6.2] - 2019-08-07

### Added

-   Added new `craft:migration` options to interactive interface
-   Updated tests to support new `craft:migration` and `craft:model` features

## [1.6.0] - 2019-07-27

### Added

-   Added artisan fallback when executing commands which dont exist in `craftsman`
    o For example, call `laravel-craftsman craft:observer TestObserver` the artiasn command `make:observer` will be executed

## [1.5.0] - 2019-07-12

### Added

-   Added `craft:publish` command to publish craftsman templates to current project
-   Removed extraneous master templates (those ending in .php)

## [1.4.0] - 2019.06.24

## [1.3.3] - 2019.05.17

### Fixed

-   Fixed rule parsing offset

## [1.3.2] - 2019.04.28

## [1.3.1] - 2019.04.24

### Added

-   Adjust rules (form request) and fields (migration) processing

## [1.3.0] - 2019.04.24

### Added

-   Added `craft:resource` command
-   Extended `craft:controller` command

### Fixed

-   Fixed issue when creating migrations, created invalid class name ([Issue 005](https://github.com/mikeerickson/laravel-craftsman/issues/5))
-   Fixed issue creating unnecessary use statement for model which is in default namespace (app directory)

## [1.2.1] - 2019-04-20

### Added

### Fixed

## [1.2.0] - 2019-04-19

### Added

-   Added `resource` controller
-   Added `craft:resource`
-   Added `resource` tests

## [1.1.2] - 2019-04-18

### Fixed

-   Fix issue when creating migrations and tablename is not supplied
-   Added migration name parsing to determine migration class name when --model or --tablename not supplied
-   Added more tests to cover migration adjustments

## [1.1.0] - 2019-04-17

### Added

-   Added new `template` option which allows passing a one off template to be used instead of using either default templates, or user defined templates in constants.

## [1.0.10] - 2019-04-06

### Added

-   Added view crafting to craft:all
-   Added --extends and --section options (for views)
-   Added --no-views to craft:all command
    Note: When using craft:all, the --no-create, --no-edit, --no-index and --no-show options are not used

## [1.0.9] - 2019-04-05

### Fixed

-   Refactor tests, cleanup

## [1.0.8] - 2019-04-04

### Added

-   Added view creation
-   Added check to error if file exists
    o Use --overwrite option to force creation
    o Refactored messages to work with file existence check

### Modified

-   Refactor asset creation to not automatically overwrite files

## [1.0.0] - 2019-03-28

### Added

-   Initial Release

[keep a changelog](https://keepachangelog.com/en/1.0.0/)
