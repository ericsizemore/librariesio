# LibrariesIO - A simple API wrapper/client for the Libraries.io API.

[![Build Status](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ericsizemore/librariesio/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ericsizemore/librariesio/?branch=master)
[![Continuous Integration](https://github.com/ericsizemore/librariesio/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ericsizemore/librariesio/actions/workflows/continuous-integration.yml)
[![Type Coverage](https://shepherd.dev/github/ericsizemore/librariesio/coverage.svg)](https://shepherd.dev/github/ericsizemore/librariesio)
[![Psalm Level](https://shepherd.dev/github/ericsizemore/librariesio/level.svg)](https://shepherd.dev/github/ericsizemore/librariesio)
[![PHPMD](https://github.com/ericsizemore/librariesio/actions/workflows/phpmd.yml/badge.svg)](https://github.com/ericsizemore/librariesio/actions/workflows/phpmd.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)
[![Downloads per Month](https://img.shields.io/packagist/dm/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)
[![License](https://img.shields.io/packagist/l/esi/librariesio.svg)](https://packagist.org/packages/esi/librariesio)

## 2.0.0 Important Note

* The `master` branch is for development of the upcoming version 2.0.0.
  * This is a notable exception to the backward compatibility promise, as most of this work was done before it was implemented.
* Should be relatively stable, but would still advise against using in production.
* Function parameters, class api's, etc. may change during development.
* The [docs](docs) have not yet been fully updated with changes.

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

## Documentation

The `docs/` folder or online [here](https://www.secondversion.com/docs/librariesio/).

## About

### Requirements

- LibrariesIO works with PHP 8.2.0 or above.
- All API requests include an api_key parameter. You will need to get your api key from your [account](https://libraries.io/account) page at libraries.io. 

### Credits

- [Eric Sizemore](https://github.com/ericsizemore)
- [All Contributors](https://github.com/ericsizemore/librariesio/contributors)

### Contributing

See [CONTRIBUTING](./CONTRIBUTING.md).

Bugs and feature requests are tracked on [GitHub](https://github.com/ericsizemore/librariesio/issues).

### Contributor Covenant Code of Conduct

See [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md)

### Backward Compatibility Promise

See [backward-compatibility.md](./backward-compatibility.md) for more information on Backwards Compatibility.

### Changelog

See the [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

### License

See the [LICENSE](./LICENSE.md) for more information on the license that applies to this project.

### Security

See [SECURITY](./SECURITY.md) for more information on the security disclosure process.
