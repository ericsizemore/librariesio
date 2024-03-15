# Repository

`Esi\LibrariesIO\LibrariesIO::repository()`

```php
/**
 * Performs a request to the 'repository' endpoint and a subset endpoint, which can be:
 * dependencies, projects, or repository
 *
 * @param array<string>|array<string, int> $options
 *
 * @throws InvalidArgumentException|ClientException|GuzzleException|RateLimitExceededException
 */
public function repository(string $endpoint, array $options): ResponseInterface;
```

The `$endpoint` paramater accepts:

`dependencies`, `projects`, or `repository`

The `$options` parameter accepts an array of key =&gt; value pairs with keys matching the 'options' array for the particular subset endpoint below:

```php
        static $repositoryParameters = [
            'dependencies' => ['format' => 'github/:owner/:name/dependencies', 'options' => ['owner', 'name']],
            'projects'     => ['format' => 'github/:owner/:name/projects'    , 'options' => ['owner', 'name']],
            'repository'   => ['format' => 'github/:owner/:name'             , 'options' => ['owner', 'name']]
        ];
```
## Endpoints

### Repository

Get info for a repository. Currently only works for open source repositories.

`GET https://libraries.io/api/github/:owner/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#repository).

### Dependencies

Get a list of dependencies for a repositories. Currently only works for open source repositories.

`GET https://libraries.io/api/github/:owner/:name/dependencies?api_key={yourApiKey}`

More information [here](https://libraries.io/api#repository-dependencies).

### Projects

Get a list of packages referencing the given repository.

`GET https://libraries.io/api/github/:owner/:name/projects?api_key={yourApiKey}`

More information [here](https://libraries.io/api#repository-projects).

#### Example

An example using the `repository()` method with the 'repository' `$endpoint` parameter.

```php
use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;

// Obviously you would want to pass your API key to the constructor, along with
// a folder/path to be used for caching requests if desired.
$api = new LibrariesIO('...yourapikey...', '...yourcachepath...');

// We call the 'repository' method with the '$endpoint' parameter set to 'repository'
// The 'repository' endpoint requires options of: owner, name
$response = $api->repository('project', ['owner' => 'gruntjs', 'name' => 'grunt']);

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

The call to `repository()` using the 'repository' endpoint and then using `Utils::raw()` will return something like:

```json
{
  "full_name": "gruntjs/grunt",
  "description": "Grunt: The JavaScript Task Runner",
  "fork": false,
  "created_at": "2011-09-21T15:16:20.000Z",
  "updated_at": "2023-07-25T02:54:47.000Z",
  "pushed_at": "2023-05-28T00:17:05.000Z",
  "homepage": "http://gruntjs.com/",
  "size": 2808,
  "stargazers_count": 12191,
  "language": "JavaScript",
  "has_issues": true,
  "has_wiki": true,
  "has_pages": false,
  "forks_count": 1536,
  "mirror_url": null,
  "open_issues_count": 160,
  "default_branch": "main",
  "subscribers_count": 489,
  "uuid": "2430537",
  "source_name": null,
  "license": "Other",
  "private": false,
  "contributions_count": 84,
  "has_readme": "README.md",
  "has_changelog": "CHANGELOG",
  "has_contributing": "CONTRIBUTING.md",
  "has_license": "LICENSE",
  "has_coc": "CODE_OF_CONDUCT.md",
  "has_threat_model": null,
  "has_audit": null,
  "status": null,
  "last_synced_at": "2023-01-31T15:57:17.204Z",
  "rank": 25,
  "host_type": "GitHub",
  "host_domain": null,
  "name": null,
  "scm": "git",
  "fork_policy": null,
  "pull_requests_enabled": null,
  "logo_url": null,
  "keywords": [
    "hacktoberfest"
  ],
  "github_contributions_count": 84,
  "github_id": "2430537"
}
```
