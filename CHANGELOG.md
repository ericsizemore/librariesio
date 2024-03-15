# CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/librariesio/compare/v1.0.0...v2.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.


## 2.0.0-dev (TBD, ~2024-04-01)

**NOTE:** Version 2.0.0 *should* be backwards compatible (for the most part) with 1.x.
However, if you make use of the `raw`, `toArray`, or `toObject` functions, please check the **CHANGED** section below.

### Changed

  * The `LibrariesIO` constructor now takes a new option `$clientOptions` which is an array of options to pass to the Guzzle Client.
    * Note, the class will ignore 'base_uri', 'http_errors', 'handler' and 'query' if passed into `$clientOptions`
  * Changed unit tests to pass a `MockHandler` instance to the class (via `$clientOptions`), which is handled in the `AbstractClient` constructor, to ease mocking and testing.
  * The `$client` property is now private.
  * `raw`, `toArray`, `toObject` are no longer part of the main `LibrariesIO` class.
    * They are instead in the `Utils` class and can be accessed statically:
      * `Utils::raw()`, `Utils::toArray()`, `Utils::toObject()`
  * The `LibrariesIO` class now only defines functions to access the API endpoints and leaves the rest of the work up to `AbstractClient` and `Utils`.
  * `Exception\RateLimitExceededException` now takes `GuzzleHttp\Exception\ClientException` as a parameter.
  * Fixes to both code and docblocks/etc. throughout per Psalm.

### Added

  * `AbstractClient` class which the main `LibrariesIO` class extends.
  * `Utils` class which holds various useful static methods used throughout the library.
  * New exceptions:
    * Exception\InvalidApiKeyException
    * Exception\InvalidEndpointException
    * Exception\InvalidEndpointOptionsException
  * `vimeo/psalm` as a dev dependency

### Removed

  * Removed the `$cachePath` property.
  * Removed unnecessary comments throughout.


## 1.1.1 (2024-03-14)

### Changed

  * Updated/refactored some code to reduce duplicate checks/etc. throughout.
  * CS improvements/fixes.

### Added

  * Added PHP-CS-Fixer to dev dependencies.

### Removed

  * Cleaned up some doc blocks and removed some unnecessary comments.


## 1.1.0 (2023-12-29)

### Changed

  * Updated `makeRequest()` with a `$method` parameter to handle post, put, and delete requests in addition to get.
  * Visibility changed to `protected` for:
    * endpointParameters()
    * processEndpointFormat()
    * verifyEndpointOptions()
  * Converted line endings to linux, some files snuck through with Windows line endings
  * Documentation updated

### Added

  * Added `subscription()` to handle adding, updating, checking and removing a subscription to a project.

### Removed

  * None


## 1.0.0 (2023-12-25)

  * Initial release
