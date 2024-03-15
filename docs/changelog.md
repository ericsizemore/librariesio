# CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/librariesio/compare/v1.0.0...v2.0.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

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
