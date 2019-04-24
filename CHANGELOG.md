# laravel-craftsman Changelog

## Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [1.3.0] - 2019.04.24

### Added

- Added `craft:resource` command
- Extended `craft:controller` command

### Fixed

- Fixed issue when creating migrations, created invalid class name ([Issue 005](https://github.com/mikeerickson/laravel-craftsman/issues/5))
- Fixed issue creating unnecessary use statement for model which is in default namespace (app directory)

## [1.2.1] - 2019-04-20

### Added

### Fixed


## [1.2.0] - 2019-04-19

### Added

- Added `resource` controller
- Added `craft:resource`
- Added `resource` tests

## [1.1.2] - 2019-04-18

### Fixed

- Fix issue when creating migrations and tablename is not supplied
- Added migration name parsing to determine migration class name when --model or --tablename not supplied
- Added more tests to cover migration adjustments

## [1.1.0] - 2019-04-17

### Added

- Added new `template` option which allows passing a one off template to be used instead of using either default templates, or user defined templates in constants.

## [1.0.10] - 2019-04-06

### Added

- Added view crafting to craft:all
- Added --extends and --section options (for views)
- Added --no-views to craft:all command
    Note: When using craft:all, the --no-create, --no-edit, --no-index and --no-show options are not used

## [1.0.9] - 2019-04-05

### Fixed

- Refactor tests, cleanup

## [1.0.8] - 2019-04-04

### Added

- Added view creation
- Added check to error if file exists
    - Use --overwrite option to force creation
- Refactored messages to work with file existence check

### Modified

- Refactor asset creation to not automatically overwrite files


## [1.0.0] - 2019-03-28

### Added

-   Initial Release


[keep a changelog](https://keepachangelog.com/en/1.0.0/)
