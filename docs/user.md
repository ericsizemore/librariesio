# User

`Esi\LibrariesIO\LibrariesIO::user()`

```php
/**
 * Performs a request to the 'user' endpoint and a subset endpoint, which can be:
 * dependencies, package_contributions, packages, repositories, repository_contributions, or subscriptions
 *
 * @param string $endpoint
 * @param array<string, int|string> $options
 * @return ResponseInterface
 * @throws InvalidArgumentException|ClientException|GuzzleException
 */
public function user(string $endpoint, array $options): ResponseInterface;
```

The `$endpoint` paramater accepts:

`dependencies`, `package_contributions`, `packages`, `repositories`, `repository_contributions`, or `subscriptions`

The `$options` parameter accepts an array of key =&gt; value pairs with keys matching the 'options' array for the particular subset endpoint below:

```php
        static $userParameters = [
            'dependencies'             => ['format' => 'github/:login/dependencies'            , 'options' => ['login']],
            'package_contributions'    => ['format' => 'github/:login/project-contributions'   , 'options' => ['login']],
            'packages'                 => ['format' => 'github/:login/projects'                , 'options' => ['login']],
            'repositories'             => ['format' => 'github/:login/repositories'            , 'options' => ['login']],
            'repository_contributions' => ['format' => 'github/:login/repository-contributions', 'options' => ['login']],
            'subscriptions'            => ['format' => 'subscriptions'                         , 'options' => []],
            'user'                     => ['format' => 'github/:login'                         , 'options' => ['login']]
        ];
```
## Endpoints

### User

Get information for a given user or organization.

`GET https://libraries.io/api/github/:login?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user).

### Repositories

Get repositories owned by a user.

`GET https://libraries.io/api/github/:login/repositories?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user-repositories)

### Packages

Get a list of packages referencing the given user's repositories.

`GET https://libraries.io/api/github/:login/projects?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user-projects)

### Package Contributions

Get a list of packages that the given user has contributed to.

`GET https://libraries.io/api/github/:login/project-contributions?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user-project-contributions)

### Repository Contributions

Get a list of repositories that the given user has contributed to.

`GET https://libraries.io/api/github/:login/repository-contributions?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user-repository-contributions)

### Dependencies

Get a list of unique packages that the given user's repositories list as a dependency. Ordered by frequency of use in those repositories.

`GET https://libraries.io/api/github/:login/dependencies?api_key={yourApiKey}`

More information [here](https://libraries.io/api#user-dependencies)

### Subscriptions

List packages that a user is subscribed to receive notifications about new releases.

`GET https://libraries.io/api/subscriptions?api_key={yourApiKey}`

More information [here](https://libraries.io/api#subscriptions-index)

#### Example

An example using the `user()` method with the 'user' `$endpoint` parameter.

```php
use Esi\LibrariesIO\LibrariesIO;

// Obviously you would want to pass your API key to the constructor, along with
// a folder/path to be used for caching requests if desired.
$api = new LibrariesIO('...yourapikey...', '...yourcachepath...');

// We call the 'user' method with the '$endpoint' parameter set to 'user'
// The 'user' endpoint requires options of: login
// In some cases 'login' just needs to be the github username, or the username/repo
$response = $api->user('user', ['login' => 'andrew']);

// From here you have a few options depending on how you need or want the data.

// For just the raw json date, we can use raw()
$json = $api->raw($response);

// To have the json decoded and handed back to you as an array, use toArray()
$json = $api->toArray($response);

// Or, to have it returned to you as an object (an \stdClass object), use toObject()
$json = $api->toObject($response);

// It is important to note that raw(), toArray(), and toObject() must have the $response as an argument.
// $response will be an instance of '\Psr\Http\Message\ResponseInterface'

// It is not recommended to attempt calling either of the to* functions back to back
```

The call to `user()` using the 'user' endpoint and then using `raw()` will return something like:

```json
{
  "id": 10639,
  "uuid": "1060",
  "login": "andrew",
  "user_type": "User",
  "created_at": "2015-01-27T07:05:17.926Z",
  "updated_at": "2022-05-16T15:28:13.424Z",
  "name": "Andrew Nesbitt",
  "company": "@ecosyste-ms and @octobox ",
  "blog": "https://nesbitt.io",
  "location": "UK",
  "hidden": false,
  "last_synced_at": "2017-04-18T11:02:01.337Z",
  "email": null,
  "bio": "Software engineer and researcher",
  "followers": 1162,
  "following": 2819,
  "host_type": "GitHub"
}
```
