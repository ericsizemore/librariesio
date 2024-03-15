# LibrariesIO - A simple API wrapper/client for the Libraries.io API.

[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/?branch=master)
[![Tests](https://github.com/ericsizemore/librariesio/actions/workflows/tests.yml/badge.svg)](https://github.com/ericsizemore/librariesio/actions/workflows/tests.yml)
[![PHPStan](https://github.com/ericsizemore/librariesio/actions/workflows/main.yml/badge.svg)](https://github.com/ericsizemore/librariesio/actions/workflows/main.yml)
[![Psalm Static analysis](https://github.com/ericsizemore/librariesio/actions/workflows/psalm.yml/badge.svg?branch=master)](https://github.com/ericsizemore/librariesio/actions/workflows/psalm.yml)
[![PHPMD](https://github.com/ericsizemore/librariesio/actions/workflows/phpmd.yml/badge.svg)](https://github.com/ericsizemore/librariesio/actions/workflows/phpmd.yml)
[![Type Coverage](https://shepherd.dev/github/ericsizemore/librariesio/coverage.svg)](https://shepherd.dev/github/ericsizemore/librariesio)
[![Psalm Level](https://shepherd.dev/github/ericsizemore/librariesio/level.svg)](https://shepherd.dev/github/ericsizemore/librariesio)
[![Latest Stable Version](https://img.shields.io/packagist/v/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)
[![Downloads per Month](https://img.shields.io/packagist/dm/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)
[![License](https://img.shields.io/packagist/l/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)

## 2.0.0 Important Note

* The `master` branch is for development of the upcoming version 2.0.0.
* Should be okay, but would still advise caution using in production.
* Function parameters, class api's, etc. may change during development.
* The [docs](docs) have not yet been updated with changes.

## Important Note

This project was born from the desire to expand my knowledge of API's and GuzzleHttp. My implementation is far from perfect, so I am open to any and all feedback that one may wish to provide.

* The Libraries.io API has the ability for pagination, however it is not yet fully implemented in this library.

## Installation

### Composer

Install the latest version with:

```bash
$ composer require esi/librariesio
```

Then, within your project (if not already included), include composer's autoload. For example:

```php
<?php

require 'vendor/autoload.php';

?>
```

For more information see the [installation](docs/installation.md) docs.

## Basic Usage

LibrariesIO splits the different endpoints based on their "component":

  * Esi\LibrariesIO\LibrariesIO::platform()
    * does not require an $endpoint, though you can pass 'platforms'.
  * Esi\LibrariesIO\LibrariesIO::project()
    * takes an 'endpoint' parameter to specify which subset you are looking for.
      * Current endpoints are:
        * contributors
        * dependencies
        * dependents
        * dependent_repositories
        * project
        * search
        * sourceRank
  * Esi\LibrariesIO\LibrariesIO::repository()
    * takes an 'endpoint' parameter to specify which subset you are looking for.
      * Current endpoints are:
        * dependencies
        * projects
        * repository
  * Esi\LibrariesIO\LibrariesIO::user()
    * takes an 'endpoint' parameter to specify which subset you are looking for.
      * Current endpoints are:
        * dependencies
        * packages
        * package_contributions
        * repositories
        * repository_contributions
        * subscriptions
        * user
  * Esi\LibrariesIO\LibrariesIO::subscription()
    * takes an 'endpoint' parameter to specify which subset you are looking for.
      * Current endpoints are:
        * subscribe
        * check
        * update
        * unsubscribe

Each 'subset' has their own required options. Check the documentation (currently WIP) for more information.

As an example, let's say you want to get a list of the available platforms. To do so:

```php
<?php

use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;

$api = new LibrariesIO('..yourapikey..', \sys_get_temp_dir());
$response = $api->platform();

print_r(Utils::toArray($response));

/*
Array
(
    [0] => Array
        (
            [name] => NPM
            [project_count] => 4079049
            [homepage] => https://www.npmjs.com
            [color] => #f1e05a
            [default_language] => JavaScript
        )

    [1] => Array
        (
            [name] => Maven
            [project_count] => 588275
            [homepage] => http://maven.org
            [color] => #b07219
            [default_language] => Java
        )
    [...]
)
*/
?>
```

For more information see the [basic usage](docs/basic-usage.md) docs.

## Testing

Unit testing is handled by PHPUnit. Given the nature of this library, with it's purpose being to interact with a live API, testing is somewhat limited.

No actual calls are made to the libraries.io API while performing tests. Instead, mock data and mocked class instances are used to test the library is working properly.

## Documentation

The `docs/` folder or online [here](https://www.secondversion.com/docs/librariesio/).

## About

### Requirements

- LibrariesIO works with PHP 8.2.0 or above.
- All API requests include an api_key parameter. You will need to get your api key from your [account](https://libraries.io/account) page at libraries.io. 

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/librariesio/issues)

Issues are the quickest way to report a bug. If you find a bug or documentation error, please check the following first:

* That there is not an Issue already open concerning the bug
* That the issue has not already been addressed (within closed Issues, for example)

### Contributing

LibrariesIO accepts contributions of code and documentation from the community. 
These contributions can be made in the form of Issues or [Pull Requests](http://help.github.com/send-pull-requests/) on the [LibrariesIO repository](https://github.com/ericsizemore/librariesio).

LibrariesIO is licensed under the MIT license. When submitting new features or patches to LibrariesIO, you are giving permission to license those features or patches under the MIT license.

LibrariesIO tries to adhere to PHPStan level 9 with strict rules and bleeding edge. Please ensure any contributions do as well.

#### Guidelines

Before we look into how, here are the guidelines. If your Pull Requests fail to pass these guidelines it will be declined, and you will need to re-submit when you’ve made the changes. This might sound a bit tough, but it is required for me to maintain quality of the code-base.

#### PHP Style

Please ensure all new contributions match the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style guide. The project is not fully PSR-12 compatible, yet; however, to ensure the easiest transition to the coding guidelines, I would like to go ahead and request that any contributions follow them.

#### Documentation

If you change anything that requires a change to documentation then you will need to add it. New methods, parameters, changing default values, adding constants, etc. are all things that will require a change to documentation. The change-log must also be updated for every change. Also, PHPDoc blocks must be maintained.

##### Documenting functions/variables (PHPDoc)

Please ensure all new contributions adhere to:

  * [PSR-5 PHPDoc](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc.md)
  * [PSR-19 PHPDoc Tags](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md)

When documenting new functions, or changing existing documentation.

#### Branching

One thing at a time: A pull request should only contain one change. That does not mean only one commit, but one change - however many commits it took. The reason for this is that if you change X and Y but send a pull request for both at the same time, we might really want X but disagree with Y, meaning we cannot merge the request. Using the Git-Flow branching model you can create new branches for both of these features and send two requests.

### Author

Eric Sizemore - <admin@secondversion.com> - <https://www.secondversion.com>

### License

LibrariesIO is licensed under the MIT License - see the `LICENSE.md` file for details
