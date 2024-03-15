# Project

`Esi\LibrariesIO\LibrariesIO::project()`

```php
/**
 * Performs a request to the 'project' endpoint and a subset endpoint, which can be:
 * contributors, dependencies, dependent_repositories, dependents, search, sourcerank, or project
 *
 * @param array<string>|array<string, int> $options
 *
 * @throws InvalidEndpointException
 * @throws ClientException
 * @throws GuzzleException
 * @throws RateLimitExceededException
 */
public function project(string $endpoint, array $options): ResponseInterface;
```

The `$endpoint` paramater accepts:

`contributors`, `dependencies`, `dependent_repositories`, `dependents`, `search`, `sourcerank`, or `project`

The `$options` parameter accepts an array of key =&gt; value pairs with keys matching the 'options' array for the particular subset endpoint below:

```php
        static $projectParameters = [
            'contributors'           => ['format' => ':platform/:name/contributors'          , 'options' => ['platform', 'name']],
            'dependencies'           => ['format' => ':platform/:name/:version/dependencies' , 'options' => ['platform', 'name', 'version']],
            'dependent_repositories' => ['format' => ':platform/:name/dependent_repositories', 'options' => ['platform', 'name']],
            'dependents'             => ['format' => ':platform/:name/dependents'            , 'options' => ['platform', 'name']],
            'search'                 => ['format' => 'search'                                , 'options' => ['query', 'sort']],
            'sourcerank'             => ['format' => ':platform/:name/sourcerank'            , 'options' => ['platform', 'name']],
            'project'                => ['format' => ':platform/:name'                       , 'options' => ['platform', 'name']]
        ];
```
## Endpoints

### Project

Get information about a package and its versions.

`GET https://libraries.io/api/:platform/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project).

### Dependencies

Get a list of dependencies for a version of a project, pass latest to get dependency info for the latest available version

`GET https://libraries.io/api/:platform/:name/:version/dependencies?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project-dependencies).

### Dependents

Get packages that have at least one version that depends on a given project.

`GET https://libraries.io/api/:platform/:name/dependents?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project-dependents)

### Dependent Repositories

Get repositories that depend on a given project.

`GET https://libraries.io/api/:platform/:name/dependent_repositories?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project-dependent-repositories).

### Contributors

Get users that have contributed to a given project.

`GET https://libraries.io/api/:platform/:name/contributors?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project-contributors)

### SourceRank

Get breakdown of SourceRank score for a given project.

`GET https://libraries.io/api/:platform/:name/sourcerank?api_key={yourApiKey}`

More information [here](https://libraries.io/api#project-sourcerank).

### Search

Search for projects

`GET https://libraries.io/api/search?q=grunt&api_key={yourApiKey}`

The search endpoint accepts a sort parameter, one of `rank`, `stars`, `dependents_count`, `dependent_repos_count`, `latest_release_published_at`, `contributions_count`, `created_at`.

The search endpoint accepts number of other parameters to filter results:

 * `languages`
 * `licenses`
 * `keywords`
 * `platforms`

More information [here](https://libraries.io/api#project-search).

#### Example

An example using the `project()` method with the 'project' `$endpoint` parameter.

```php
use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;

// Obviously you would want to pass your API key to the constructor, along with
// a folder/path to be used for caching requests if desired.
$api = new LibrariesIO('...yourapikey...', '...yourcachepath...');

// We call the 'project' method with the '$endpoint' parameter set to 'project'
// The 'project' endpoint requires options of: platform, name
$response = $api->project('project', ['platform' => 'npm', 'name' => 'base62']);

// From here you have a few options depending on how you need or want the data.

// For just the raw json date, we can use Utils::raw()
$json = Utils::raw($response);

// To have the json decoded and handed back to you as an array, use Utils::toArray()
$json = Utils::toArray($response);

// Or, to have it returned to you as an object (an \stdClass object), use Utils::toObject()
$json = Utils::toObject($response);

// It is important to note that raw(), toArray(), and toObject() must have the $response as an argument.
// $response will be an instance of '\Psr\Http\Message\ResponseInterface'

// It is not recommended to attempt calling either of the to* functions back to back
```

The call to `project()` using the 'project' endpoint and then using `Utils::raw()` will return something like:

```json
{
  "contributions_count": 11,
  "dependent_repos_count": 20489,
  "dependents_count": 54,
  "deprecation_reason": null,
  "description": "JavaScript Base62 encode/decoder",
  "forks": 26,
  "homepage": "https://github.com/base62/base62.js",
  "keywords": [
    "base-62",
    "encoder",
    "decoder",
    "base62",
    "encoding",
    "javascript"
  ],
  "language": "JavaScript",
  "latest_download_url": "https://registry.npmjs.org/base62/-/base62-2.0.1.tgz",
  "latest_release_number": "2.0.1",
  "latest_release_published_at": "2019-03-06 15:06:40 UTC",
  "latest_stable_release_number": "2.0.1",
  "latest_stable_release_published_at": "2019-03-06 15:06:40 UTC",
  "license_normalized": false,
  "licenses": "MIT",
  "name": "base62",
  "normalized_licenses": [
    "MIT"
  ],
  "package_manager_url": "https://www.npmjs.com/package/base62",
  "platform": "NPM",
  "rank": 20,
  "repository_license": "MIT",
  "repository_status": null,
  "repository_url": "https://github.com/base62/base62.js",
  "stars": 128,
  "status": null,
  "versions": [
    {
      "number": "0.1.0",
      "published_at": "2012-02-24 18:04:06 UTC",
      "spdx_expression": "NONE",
      "original_license": "",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "0.1.1",
      "published_at": "2012-12-09 05:11:27 UTC",
      "spdx_expression": "NONE",
      "original_license": "",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "0.1.2",
      "published_at": "2014-07-15 21:24:45 UTC",
      "spdx_expression": "NONE",
      "original_license": "",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.0.0",
      "published_at": "2014-10-11 07:22:23 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.1.0",
      "published_at": "2015-02-23 09:52:54 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.1.1",
      "published_at": "2016-04-14 21:55:22 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.1.2",
      "published_at": "2016-11-14 00:43:51 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.0",
      "published_at": "2017-05-15 11:26:01 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.1",
      "published_at": "2017-11-14 08:38:56 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.4",
      "published_at": "2018-02-10 21:54:23 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.5",
      "published_at": "2018-02-10 23:16:39 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.6",
      "published_at": "2018-02-14 12:24:12 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.7",
      "published_at": "2018-02-14 12:46:17 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "1.2.8",
      "published_at": "2018-03-30 17:15:14 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "2.0.0",
      "published_at": "2018-04-13 09:18:23 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    },
    {
      "number": "2.0.1",
      "published_at": "2019-03-06 15:06:40 UTC",
      "spdx_expression": "MIT",
      "original_license": "MIT",
      "researched_at": null,
      "repository_sources": [
        "NPM"
      ]
    }
  ]
}
```
