## CHANGELOG
A not so exhaustive list of changes for each release.

For a more detailed listing of changes between each version, 
you can use the following url: https://github.com/ericsizemore/librariesio/compare/v1.0.0...v1.1.0. 

Simply replace the version numbers depending on which set of changes you wish to see.

### 1.1.1 ()

  * Added Rector and PHP-CS-Fixer to dev dependencies.
  * Updated/refactored some code to reduce duplicate checks/etc. throughout.
  * CS improvements/fixes.

### 1.1.0 (2023-12-29)

  * Added `subscription()` to handle adding, updating, checking and removing a subscription to a project.
  * Updated `makeRequest()` with a `$method` parameter to handle post, put, and delete requests in addition to get.
  * Visibility changed to `protected` for:
    * endpointParameters()
    * processEndpointFormat()
    * verifyEndpointOptions()
  * Converted line endings to linux, some files snuck through with Windows line endings
  * Documentation updated

### 1.0.0 (2023-12-25)

  * Initial release
