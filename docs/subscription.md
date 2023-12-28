# User

`Esi\LibrariesIO\LibrariesIO::subscription()`

```php
/**
 * Performs a request to the 'subscription' endpoint and a subset endpoint, which can be:
 * subscribe, check, update, unsubscribe
 *
 * @param string $endpoint
 * @param array<string, int|string> $options
 * @return ResponseInterface
 * @throws InvalidArgumentException|ClientException|GuzzleException
 */
public function subscription(string $endpoint, array $options): ResponseInterface;
```

The `$endpoint` paramater accepts:

`subscribe`, `check`, `update`, or `unsubscribe`

The `$options` parameter accepts an array of key =&gt; value pairs with keys matching the 'options' array for the particular subset endpoint below:

```php
        static $subscriptionParameters = [
            'subscribe'   => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease']],
            'check'       => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name']],
            'update'      => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name', 'include_prerelease']],
            'unsubscribe' => ['format' => 'subscriptions/:platform/:name', 'options' => ['platform', 'name']]
        ];
```
## Endpoints

### Subscribe

Subscribe to receive notifications about new releases of a project.

Parameters: `include_prerelease`

`POST https://libraries.io/api/subscriptions/:platform/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#subscriptions-create).

### Check

Check if a users is subscribed to receive notifications about new releases of a project.

`GET https://libraries.io/api/subscriptions/:platform/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#subscriptions-show)

### Update

Update the options for a subscription

Parameters: `include_prerelease`

`PUT https://libraries.io/api/subscriptions/:platform/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#subscriptions-update)

### Unsubscribe

Stop receiving release notifications from a project.

`DELETE https://libraries.io/api/subscriptions/:platform/:name?api_key={yourApiKey}`

More information [here](https://libraries.io/api#subscriptions-destroy)

#### Example

An example using the `subscription()` method with the 'subscribe' `$endpoint` parameter.

```php
use Esi\LibrariesIO\LibrariesIO;

// Obviously you would want to pass your API key to the constructor, along with
// a folder/path to be used for caching requests if desired.
$api = new LibrariesIO('...yourapikey...', '...yourcachepath...');

// We call the 'subscription' method with the '$endpoint' parameter set to 'subscribe'
// The 'subscribe' endpoint requires options of: platform, name, include_prerelease
$response = $api->subscription('subscribe', ['platform' => 'npm', 'name' => 'utility', 'include_prerelease' => 'true']);

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

The call to `subscription()` using the 'subscribe' endpoint and then using `raw()` will return something like:

```json
{
  "created_at": "2015-02-22T00:24:10.950Z",
  "updated_at": "2015-02-22T00:24:10.950Z",
  "include_prerelease": true,
  "project": {
    "id": 183039,
    "name": "rails",
    "platform": "Rubygems",
    "created_at": "2015-01-28T10:30:55.520Z",
    "updated_at": "2023-12-01T06:40:49.238Z",
    "description": "Ruby on Rails is a full-stack web framework optimized for programmer happiness and sustainable productivity. It encourages beautiful code by favoring convention over configuration.",
    "keywords": [
      "activejob",
      "activerecord",
      "framework",
      "html",
      "mvc",
      "rails",
      "ruby"
    ],
    "homepage": "https://rubyonrails.org",
    "licenses": "MIT",
    "repository_url": "https://github.com/rails/rails",
    "repository_id": 21734,
    "normalized_licenses": [
      "MIT"
    ],
    "versions_count": 460,
    "rank": 31,
    "latest_release_published_at": "2023-11-10T21:52:10.943Z",
    "latest_release_number": "7.1.2",
    "pm_id": null,
    "keywords_array": [

    ],
    "dependents_count": 14207,
    "language": "Ruby",
    "status": null,
    "last_synced_at": "2023-11-10T21:56:25.515Z",
    "dependent_repos_count": 542430,
    "runtime_dependencies_count": 13,
    "score": 89,
    "score_last_calculated": "2018-08-08T11:01:18.457Z",
    "latest_stable_release_number": "7.1.2",
    "latest_stable_release_published_at": "2023-11-10T21:52:10.943Z",
    "license_set_by_admin": false,
    "license_normalized": false,
    "deprecation_reason": null,
    "status_checked_at": "2023-12-26T04:32:07.693Z",
    "lifted": false,
    "package_manager_url": "https://rubygems.org/gems/rails",
    "stars": 53939,
    "forks": 21460,
    "latest_stable_release": {
      "id": 82733226,
      "project_id": 183039,
      "number": "7.1.2",
      "published_at": "2023-11-10T21:52:10.943Z",
      "created_at": "2023-11-10T21:55:48.041Z",
      "updated_at": "2023-11-10T21:55:48.041Z",
      "runtime_dependencies_count": 13,
      "spdx_expression": "MIT",
      "original_license": [
        "MIT"
      ],
      "researched_at": null,
      "repository_sources": null,
      "status": null,
      "dependencies_count": 13
    },
    "versions": [
      {
        "number": "0.10.0",
        "published_at": "2009-07-25T18:01:58.000Z"
      },
      {
        "number": "0.10.1",
        "published_at": "2009-07-25T18:01:58.000Z"
      },
      {
        "number": "0.11.0",
        "published_at": "2009-07-25T18:01:58.000Z"
      },
      {
        "number": "0.11.1",
        "published_at": "2009-07-25T18:01:58.000Z"
      },
      {
        "number": "0.12.0",
        "published_at": "2009-07-25T18:01:58.000Z"
      },
      {
        "number": "0.12.1",
        "published_at": "2009-07-25T18:01:57.000Z"
      },
      {
        "number": "0.13.0",
        "published_at": "2009-07-25T18:01:57.000Z"
      },
      {
        "number": "0.13.1",
        "published_at": "2009-07-25T18:01:57.000Z"
      },
      {
        "number": "0.14.1",
        "published_at": "2009-07-25T18:01:57.000Z"
      },
      {
        "number": "0.14.2",
        "published_at": "2009-07-25T18:01:57.000Z"
      },
      {
        "number": "0.14.3",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.14.4",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.8.0",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.8.5",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.9.0",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.9.1",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.9.2",
        "published_at": "2009-07-25T18:01:56.000Z"
      },
      {
        "number": "0.9.3",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "0.9.4",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "0.9.4.1",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "0.9.5",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "1.0.0",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "1.1.0",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "1.1.1",
        "published_at": "2009-07-25T18:01:55.000Z"
      },
      {
        "number": "1.1.2",
        "published_at": "2009-07-25T18:01:54.000Z"
      },
      {
        "number": "1.1.3",
        "published_at": "2009-07-25T18:01:54.000Z"
      },
      {
        "number": "1.1.4",
        "published_at": "2009-07-25T18:01:54.000Z"
      },
      {
        "number": "1.1.5",
        "published_at": "2009-07-25T18:01:54.000Z"
      },
      {
        "number": "1.1.6",
        "published_at": "2009-07-25T18:01:54.000Z"
      },
      {
        "number": "1.2.0",
        "published_at": "2009-07-25T18:01:53.000Z"
      },
      {
        "number": "1.2.1",
        "published_at": "2009-07-25T18:01:52.000Z"
      },
      {
        "number": "1.2.2",
        "published_at": "2009-07-25T18:01:52.000Z"
      },
      {
        "number": "1.2.3",
        "published_at": "2009-07-25T18:01:52.000Z"
      },
      {
        "number": "1.2.4",
        "published_at": "2009-07-25T18:01:52.000Z"
      },
      {
        "number": "1.2.5",
        "published_at": "2009-07-25T18:01:52.000Z"
      },
      {
        "number": "1.2.6",
        "published_at": "2009-07-25T18:01:51.000Z"
      },
      {
        "number": "2.0.0",
        "published_at": "2009-07-25T18:01:51.000Z"
      },
      {
        "number": "2.0.1",
        "published_at": "2009-07-25T18:01:51.000Z"
      },
      {
        "number": "2.0.2",
        "published_at": "2009-07-25T18:01:51.000Z"
      },
      {
        "number": "2.0.4",
        "published_at": "2009-07-25T18:01:51.000Z"
      },
      {
        "number": "2.0.5",
        "published_at": "2009-07-25T18:01:50.000Z"
      },
      {
        "number": "2.1.0",
        "published_at": "2009-07-25T18:01:50.000Z"
      },
      {
        "number": "2.1.1",
        "published_at": "2009-07-25T18:01:50.000Z"
      },
      {
        "number": "2.1.2",
        "published_at": "2009-07-25T18:01:50.000Z"
      },
      {
        "number": "2.2.2",
        "published_at": "2009-07-25T18:01:49.000Z"
      },
      {
        "number": "2.2.3",
        "published_at": "2009-09-28T09:25:13.132Z"
      },
      {
        "number": "2.3.10",
        "published_at": "2010-10-14T20:53:17.413Z"
      },
      {
        "number": "2.3.11",
        "published_at": "2011-02-08T21:17:36.254Z"
      },
      {
        "number": "2.3.12",
        "published_at": "2011-06-08T00:22:06.357Z"
      },
      {
        "number": "2.3.14",
        "published_at": "2011-08-16T22:01:21.962Z"
      },
      {
        "number": "2.3.15",
        "published_at": "2013-01-08T20:08:28.812Z"
      },
      {
        "number": "2.3.16",
        "published_at": "2013-01-28T21:01:30.451Z"
      },
      {
        "number": "2.3.17",
        "published_at": "2013-02-11T18:17:30.726Z"
      },
      {
        "number": "2.3.18",
        "published_at": "2013-03-18T17:13:25.422Z"
      },
      {
        "number": "2.3.2",
        "published_at": "2009-07-25T18:01:49.000Z"
      },
      {
        "number": "2.3.3",
        "published_at": "2009-08-05T13:21:07.000Z"
      },
      {
        "number": "2.3.4",
        "published_at": "2009-09-04T17:33:48.000Z"
      },
      {
        "number": "2.3.5",
        "published_at": "2009-11-27T00:12:56.921Z"
      },
      {
        "number": "2.3.6",
        "published_at": "2010-05-23T07:49:23.602Z"
      },
      {
        "number": "2.3.7",
        "published_at": "2010-05-24T08:23:05.731Z"
      },
      {
        "number": "2.3.8",
        "published_at": "2010-05-25T04:53:06.895Z"
      },
      {
        "number": "2.3.8.pre1",
        "published_at": "2010-05-24T21:17:25.987Z"
      },
      {
        "number": "2.3.9",
        "published_at": "2010-09-04T21:54:41.257Z"
      },
      {
        "number": "2.3.9.pre",
        "published_at": "2010-08-30T03:32:34.689Z"
      },
      {
        "number": "3.0.0",
        "published_at": "2010-08-29T23:11:11.490Z"
      },
      {
        "number": "3.0.0.beta",
        "published_at": "2010-02-05T03:02:19.496Z"
      },
      {
        "number": "3.0.0.beta2",
        "published_at": "2010-04-01T21:26:26.222Z"
      },
      {
        "number": "3.0.0.beta3",
        "published_at": "2010-04-13T19:23:14.932Z"
      },
      {
        "number": "3.0.0.beta4",
        "published_at": "2010-06-08T22:33:16.046Z"
      },
      {
        "number": "3.0.0.rc",
        "published_at": "2010-07-26T21:43:12.765Z"
      },
      {
        "number": "3.0.0.rc2",
        "published_at": "2010-08-24T03:04:45.033Z"
      },
      {
        "number": "3.0.1",
        "published_at": "2010-10-14T20:55:44.846Z"
      },
      {
        "number": "3.0.10",
        "published_at": "2011-08-16T22:14:17.045Z"
      },
      {
        "number": "3.0.10.rc1",
        "published_at": "2011-08-05T00:12:05.290Z"
      },
      {
        "number": "3.0.11",
        "published_at": "2011-11-18T01:23:23.249Z"
      },
      {
        "number": "3.0.12",
        "published_at": "2012-03-01T17:52:15.609Z"
      },
      {
        "number": "3.0.12.rc1",
        "published_at": "2012-02-22T21:39:19.764Z"
      },
      {
        "number": "3.0.13",
        "published_at": "2012-05-31T18:24:59.747Z"
      },
      {
        "number": "3.0.13.rc1",
        "published_at": "2012-05-28T19:01:47.715Z"
      },
      {
        "number": "3.0.14",
        "published_at": "2012-06-12T21:26:07.460Z"
      },
      {
        "number": "3.0.15",
        "published_at": "2012-06-13T03:07:06.509Z"
      },
      {
        "number": "3.0.16",
        "published_at": "2012-07-26T22:08:54.212Z"
      },
      {
        "number": "3.0.17",
        "published_at": "2012-08-09T21:16:44.882Z"
      },
      {
        "number": "3.0.18",
        "published_at": "2013-01-02T21:19:52.960Z"
      },
      {
        "number": "3.0.19",
        "published_at": "2013-01-08T20:08:33.922Z"
      },
      {
        "number": "3.0.2",
        "published_at": "2010-11-15T19:33:41.460Z"
      },
      {
        "number": "3.0.20",
        "published_at": "2013-01-28T21:01:34.374Z"
      },
      {
        "number": "3.0.3",
        "published_at": "2010-11-16T16:29:00.892Z"
      },
      {
        "number": "3.0.4",
        "published_at": "2011-02-08T21:17:48.221Z"
      },
      {
        "number": "3.0.4.rc1",
        "published_at": "2011-01-30T23:00:37.572Z"
      },
      {
        "number": "3.0.5",
        "published_at": "2011-02-27T02:30:55.377Z"
      },
      {
        "number": "3.0.5.rc1",
        "published_at": "2011-02-23T19:08:34.691Z"
      },
      {
        "number": "3.0.6",
        "published_at": "2011-04-05T23:05:21.745Z"
      },
      {
        "number": "3.0.6.rc1",
        "published_at": "2011-03-29T20:47:15.107Z"
      },
      {
        "number": "3.0.6.rc2",
        "published_at": "2011-03-31T05:28:51.216Z"
      },
      {
        "number": "3.0.7",
        "published_at": "2011-04-18T21:05:54.308Z"
      },
      {
        "number": "3.0.7.rc1",
        "published_at": "2011-04-14T21:57:06.386Z"
      },
      {
        "number": "3.0.7.rc2",
        "published_at": "2011-04-15T17:33:53.132Z"
      },
      {
        "number": "3.0.8",
        "published_at": "2011-06-08T00:16:45.270Z"
      },
      {
        "number": "3.0.8.rc1",
        "published_at": "2011-05-26T00:11:36.891Z"
      },
      {
        "number": "3.0.8.rc2",
        "published_at": "2011-05-27T16:32:24.502Z"
      },
      {
        "number": "3.0.8.rc4",
        "published_at": "2011-05-31T00:08:18.745Z"
      },
      {
        "number": "3.0.9",
        "published_at": "2011-06-16T10:05:11.080Z"
      },
      {
        "number": "3.0.9.rc1",
        "published_at": "2011-06-08T21:20:17.404Z"
      },
      {
        "number": "3.0.9.rc3",
        "published_at": "2011-06-09T22:51:39.349Z"
      },
      {
        "number": "3.0.9.rc4",
        "published_at": "2011-06-12T21:24:34.980Z"
      },
      {
        "number": "3.0.9.rc5",
        "published_at": "2011-06-12T21:30:07.555Z"
      },
      {
        "number": "3.1.0",
        "published_at": "2011-08-31T02:18:30.035Z"
      },
      {
        "number": "3.1.0.beta1",
        "published_at": "2011-05-05T01:23:18.105Z"
      },
      {
        "number": "3.1.0.rc1",
        "published_at": "2011-05-22T02:26:25.383Z"
      },
      {
        "number": "3.1.0.rc2",
        "published_at": "2011-06-08T00:16:57.976Z"
      },
      {
        "number": "3.1.0.rc3",
        "published_at": "2011-06-08T21:27:28.270Z"
      },
      {
        "number": "3.1.0.rc4",
        "published_at": "2011-06-09T22:56:24.880Z"
      },
      {
        "number": "3.1.0.rc5",
        "published_at": "2011-07-25T23:05:19.817Z"
      },
      {
        "number": "3.1.0.rc6",
        "published_at": "2011-08-16T22:33:32.921Z"
      },
      {
        "number": "3.1.0.rc8",
        "published_at": "2011-08-29T03:27:19.194Z"
      },
      {
        "number": "3.1.1",
        "published_at": "2011-10-07T15:30:09.628Z"
      },
      {
        "number": "3.1.10",
        "published_at": "2013-01-08T20:08:37.727Z"
      },
      {
        "number": "3.1.11",
        "published_at": "2013-02-11T18:17:37.200Z"
      },
      {
        "number": "3.1.12",
        "published_at": "2013-03-18T17:13:29.344Z"
      },
      {
        "number": "3.1.1.rc1",
        "published_at": "2011-09-15T00:27:03.617Z"
      },
      {
        "number": "3.1.1.rc2",
        "published_at": "2011-09-29T22:17:03.417Z"
      },
      {
        "number": "3.1.1.rc3",
        "published_at": "2011-10-06T02:31:00.452Z"
      },
      {
        "number": "3.1.2",
        "published_at": "2011-11-18T01:33:32.509Z"
      },
      {
        "number": "3.1.2.rc1",
        "published_at": "2011-11-14T14:17:34.523Z"
      },
      {
        "number": "3.1.2.rc2",
        "published_at": "2011-11-14T15:49:20.198Z"
      },
      {
        "number": "3.1.3",
        "published_at": "2011-11-20T22:52:57.492Z"
      },
      {
        "number": "3.1.4",
        "published_at": "2012-03-01T17:52:28.342Z"
      },
      {
        "number": "3.1.4.rc1",
        "published_at": "2012-02-22T21:39:29.633Z"
      },
      {
        "number": "3.1.5",
        "published_at": "2012-05-31T18:25:06.617Z"
      },
      {
        "number": "3.1.5.rc1",
        "published_at": "2012-05-28T19:01:51.050Z"
      },
      {
        "number": "3.1.6",
        "published_at": "2012-06-12T21:26:16.856Z"
      },
      {
        "number": "3.1.7",
        "published_at": "2012-07-26T22:09:00.975Z"
      },
      {
        "number": "3.1.8",
        "published_at": "2012-08-09T21:20:27.129Z"
      },
      {
        "number": "3.1.9",
        "published_at": "2013-01-02T21:19:56.845Z"
      },
      {
        "number": "3.2.0",
        "published_at": "2012-01-20T16:47:48.848Z"
      },
      {
        "number": "3.2.0.rc1",
        "published_at": "2011-12-20T00:41:10.661Z"
      },
      {
        "number": "3.2.0.rc2",
        "published_at": "2012-01-04T21:05:27.454Z"
      },
      {
        "number": "3.2.1",
        "published_at": "2012-01-26T23:09:41.494Z"
      },
      {
        "number": "3.2.10",
        "published_at": "2013-01-02T21:20:01.186Z"
      },
      {
        "number": "3.2.11",
        "published_at": "2013-01-08T20:08:45.798Z"
      },
      {
        "number": "3.2.12",
        "published_at": "2013-02-11T18:17:41.481Z"
      },
      {
        "number": "3.2.13",
        "published_at": "2013-03-18T17:13:33.058Z"
      },
      {
        "number": "3.2.13.rc1",
        "published_at": "2013-02-27T20:25:46.062Z"
      },
      {
        "number": "3.2.13.rc2",
        "published_at": "2013-03-06T23:06:19.052Z"
      },
      {
        "number": "3.2.14",
        "published_at": "2013-07-22T16:44:50.870Z"
      },
      {
        "number": "3.2.14.rc1",
        "published_at": "2013-07-13T00:25:39.110Z"
      },
      {
        "number": "3.2.14.rc2",
        "published_at": "2013-07-16T16:13:33.339Z"
      },
      {
        "number": "3.2.15",
        "published_at": "2013-10-16T17:23:10.503Z"
      },
      {
        "number": "3.2.15.rc1",
        "published_at": "2013-10-03T18:54:09.709Z"
      },
      {
        "number": "3.2.15.rc2",
        "published_at": "2013-10-04T20:48:45.484Z"
      },
      {
        "number": "3.2.15.rc3",
        "published_at": "2013-10-11T21:17:17.374Z"
      },
      {
        "number": "3.2.16",
        "published_at": "2013-12-03T19:01:19.549Z"
      },
      {
        "number": "3.2.17",
        "published_at": "2014-02-18T18:54:56.443Z"
      },
      {
        "number": "3.2.18",
        "published_at": "2014-05-06T16:17:02.829Z"
      },
      {
        "number": "3.2.19",
        "published_at": "2014-07-02T17:02:48.733Z"
      },
      {
        "number": "3.2.2",
        "published_at": "2012-03-01T17:52:33.094Z"
      },
      {
        "number": "3.2.20",
        "published_at": "2014-10-30T18:37:26.434Z"
      },
      {
        "number": "3.2.21",
        "published_at": "2014-11-17T16:00:44.994Z"
      },
      {
        "number": "3.2.22",
        "published_at": "2015-06-16T18:06:38.294Z"
      },
      {
        "number": "3.2.22.1",
        "published_at": "2016-01-25T19:26:12.364Z"
      },
      {
        "number": "3.2.22.2",
        "published_at": "2016-02-29T19:24:19.757Z"
      },
      {
        "number": "3.2.22.3",
        "published_at": "2016-08-11T17:34:59.710Z"
      },
      {
        "number": "3.2.22.4",
        "published_at": "2016-08-11T19:20:46.883Z"
      },
      {
        "number": "3.2.22.5",
        "published_at": "2016-09-14T21:19:01.962Z"
      },
      {
        "number": "3.2.2.rc1",
        "published_at": "2012-02-22T21:39:35.308Z"
      },
      {
        "number": "3.2.3",
        "published_at": "2012-03-30T22:26:20.685Z"
      },
      {
        "number": "3.2.3.rc1",
        "published_at": "2012-03-27T17:11:24.443Z"
      },
      {
        "number": "3.2.3.rc2",
        "published_at": "2012-03-29T16:14:14.715Z"
      },
      {
        "number": "3.2.4",
        "published_at": "2012-05-31T18:25:13.532Z"
      },
      {
        "number": "3.2.4.rc1",
        "published_at": "2012-05-28T19:01:55.834Z"
      },
      {
        "number": "3.2.5",
        "published_at": "2012-06-01T03:39:04.678Z"
      },
      {
        "number": "3.2.6",
        "published_at": "2012-06-12T21:26:21.434Z"
      },
      {
        "number": "3.2.7",
        "published_at": "2012-07-26T22:09:06.275Z"
      },
      {
        "number": "3.2.7.rc1",
        "published_at": "2012-07-23T21:45:55.204Z"
      },
      {
        "number": "3.2.8",
        "published_at": "2012-08-09T21:23:34.632Z"
      },
      {
        "number": "3.2.8.rc1",
        "published_at": "2012-08-01T20:57:56.061Z"
      },
      {
        "number": "3.2.8.rc2",
        "published_at": "2012-08-03T14:29:05.254Z"
      },
      {
        "number": "3.2.9",
        "published_at": "2012-11-12T15:21:34.822Z"
      },
      {
        "number": "3.2.9.rc1",
        "published_at": "2012-10-29T17:07:08.109Z"
      },
      {
        "number": "3.2.9.rc2",
        "published_at": "2012-11-01T17:39:37.178Z"
      },
      {
        "number": "3.2.9.rc3",
        "published_at": "2012-11-09T18:00:50.077Z"
      },
      {
        "number": "4.0.0",
        "published_at": "2013-06-25T14:32:58.526Z"
      },
      {
        "number": "4.0.0.beta1",
        "published_at": "2013-02-26T00:05:43.566Z"
      },
      {
        "number": "4.0.0.rc1",
        "published_at": "2013-04-29T15:39:05.085Z"
      },
      {
        "number": "4.0.0.rc2",
        "published_at": "2013-06-11T20:26:00.144Z"
      },
      {
        "number": "4.0.1",
        "published_at": "2013-11-01T19:08:16.307Z"
      },
      {
        "number": "4.0.10",
        "published_at": "2014-09-11T17:33:15.455Z"
      },
      {
        "number": "4.0.10.rc1",
        "published_at": "2014-08-19T20:48:29.471Z"
      },
      {
        "number": "4.0.10.rc2",
        "published_at": "2014-09-08T17:55:45.314Z"
      },
      {
        "number": "4.0.11",
        "published_at": "2014-10-30T18:37:38.192Z"
      },
      {
        "number": "4.0.11.1",
        "published_at": "2014-11-19T19:09:54.075Z"
      },
      {
        "number": "4.0.12",
        "published_at": "2014-11-17T16:01:00.306Z"
      },
      {
        "number": "4.0.13",
        "published_at": "2015-01-06T20:08:59.935Z"
      },
      {
        "number": "4.0.13.rc1",
        "published_at": "2015-01-02T00:54:54.587Z"
      },
      {
        "number": "4.0.1.rc1",
        "published_at": "2013-10-17T16:46:23.993Z"
      },
      {
        "number": "4.0.1.rc2",
        "published_at": "2013-10-21T22:01:19.341Z"
      },
      {
        "number": "4.0.1.rc3",
        "published_at": "2013-10-23T21:41:08.791Z"
      },
      {
        "number": "4.0.1.rc4",
        "published_at": "2013-10-30T20:49:25.297Z"
      },
      {
        "number": "4.0.2",
        "published_at": "2013-12-03T19:01:29.867Z"
      },
      {
        "number": "4.0.3",
        "published_at": "2014-02-18T18:49:43.150Z"
      },
      {
        "number": "4.0.4",
        "published_at": "2014-03-14T17:37:07.331Z"
      },
      {
        "number": "4.0.4.rc1",
        "published_at": "2014-03-11T17:31:18.568Z"
      },
      {
        "number": "4.0.5",
        "published_at": "2014-05-06T16:13:27.132Z"
      },
      {
        "number": "4.0.6",
        "published_at": "2014-06-26T16:30:13.579Z"
      },
      {
        "number": "4.0.6.rc1",
        "published_at": "2014-05-27T16:06:55.364Z"
      },
      {
        "number": "4.0.6.rc2",
        "published_at": "2014-06-16T16:16:01.642Z"
      },
      {
        "number": "4.0.6.rc3",
        "published_at": "2014-06-23T17:24:41.466Z"
      },
      {
        "number": "4.0.7",
        "published_at": "2014-07-02T17:04:32.418Z"
      },
      {
        "number": "4.0.8",
        "published_at": "2014-07-02T19:42:37.603Z"
      },
      {
        "number": "4.0.9",
        "published_at": "2014-08-18T17:03:01.087Z"
      },
      {
        "number": "4.1.0",
        "published_at": "2014-04-08T19:21:51.275Z"
      },
      {
        "number": "4.1.0.beta1",
        "published_at": "2013-12-18T00:15:16.640Z"
      },
      {
        "number": "4.1.0.beta2",
        "published_at": "2014-02-18T18:52:57.614Z"
      },
      {
        "number": "4.1.0.rc1",
        "published_at": "2014-02-18T20:59:23.632Z"
      },
      {
        "number": "4.1.0.rc2",
        "published_at": "2014-03-25T20:12:47.195Z"
      },
      {
        "number": "4.1.1",
        "published_at": "2014-05-06T16:11:31.458Z"
      },
      {
        "number": "4.1.10",
        "published_at": "2015-03-19T16:50:27.388Z"
      },
      {
        "number": "4.1.10.rc1",
        "published_at": "2015-02-20T22:25:09.666Z"
      },
      {
        "number": "4.1.10.rc2",
        "published_at": "2015-02-25T22:22:40.645Z"
      },
      {
        "number": "4.1.10.rc3",
        "published_at": "2015-03-02T21:39:47.964Z"
      },
      {
        "number": "4.1.10.rc4",
        "published_at": "2015-03-12T21:32:52.724Z"
      },
      {
        "number": "4.1.11",
        "published_at": "2015-06-16T18:00:13.043Z"
      },
      {
        "number": "4.1.12",
        "published_at": "2015-06-25T21:26:08.544Z"
      },
      {
        "number": "4.1.12.rc1",
        "published_at": "2015-06-22T14:05:08.486Z"
      },
      {
        "number": "4.1.13",
        "published_at": "2015-08-24T18:02:56.741Z"
      },
      {
        "number": "4.1.13.rc1",
        "published_at": "2015-08-14T15:13:26.943Z"
      },
      {
        "number": "4.1.14",
        "published_at": "2015-11-12T18:20:40.613Z"
      },
      {
        "number": "4.1.14.1",
        "published_at": "2016-01-25T19:26:27.339Z"
      },
      {
        "number": "4.1.14.2",
        "published_at": "2016-02-29T19:19:55.523Z"
      },
      {
        "number": "4.1.14.rc1",
        "published_at": "2015-10-30T20:45:42.801Z"
      },
      {
        "number": "4.1.14.rc2",
        "published_at": "2015-11-05T02:55:44.276Z"
      },
      {
        "number": "4.1.15",
        "published_at": "2016-03-07T22:37:14.594Z"
      },
      {
        "number": "4.1.15.rc1",
        "published_at": "2016-03-01T18:43:40.764Z"
      },
      {
        "number": "4.1.16",
        "published_at": "2016-07-12T22:20:56.527Z"
      },
      {
        "number": "4.1.16.rc1",
        "published_at": "2016-07-02T02:15:20.923Z"
      },
      {
        "number": "4.1.2",
        "published_at": "2014-06-26T14:50:09.079Z"
      },
      {
        "number": "4.1.2.rc1",
        "published_at": "2014-05-27T16:12:48.106Z"
      },
      {
        "number": "4.1.2.rc2",
        "published_at": "2014-06-16T16:30:46.332Z"
      },
      {
        "number": "4.1.2.rc3",
        "published_at": "2014-06-23T17:28:46.002Z"
      },
      {
        "number": "4.1.3",
        "published_at": "2014-07-02T17:06:42.181Z"
      },
      {
        "number": "4.1.4",
        "published_at": "2014-07-02T19:53:35.556Z"
      },
      {
        "number": "4.1.5",
        "published_at": "2014-08-18T17:01:03.727Z"
      },
      {
        "number": "4.1.6",
        "published_at": "2014-09-11T17:26:04.576Z"
      },
      {
        "number": "4.1.6.rc1",
        "published_at": "2014-08-19T20:52:47.110Z"
      },
      {
        "number": "4.1.6.rc2",
        "published_at": "2014-09-08T18:13:12.723Z"
      },
      {
        "number": "4.1.7",
        "published_at": "2014-10-30T18:37:49.213Z"
      },
      {
        "number": "4.1.7.1",
        "published_at": "2014-11-19T19:12:12.692Z"
      },
      {
        "number": "4.1.8",
        "published_at": "2014-11-17T16:01:13.385Z"
      },
      {
        "number": "4.1.9",
        "published_at": "2015-01-06T20:04:31.185Z"
      },
      {
        "number": "4.1.9.rc1",
        "published_at": "2015-01-02T01:11:10.973Z"
      },
      {
        "number": "4.2.0",
        "published_at": "2014-12-20T00:15:37.476Z"
      },
      {
        "number": "4.2.0.beta1",
        "published_at": "2014-08-20T02:34:44.046Z"
      },
      {
        "number": "4.2.0.beta2",
        "published_at": "2014-09-29T17:16:38.761Z"
      },
      {
        "number": "4.2.0.beta3",
        "published_at": "2014-10-30T18:37:59.690Z"
      },
      {
        "number": "4.2.0.beta4",
        "published_at": "2014-10-30T22:13:30.689Z"
      },
      {
        "number": "4.2.0.rc1",
        "published_at": "2014-11-28T17:53:27.822Z"
      },
      {
        "number": "4.2.0.rc2",
        "published_at": "2014-12-05T23:20:12.824Z"
      },
      {
        "number": "4.2.0.rc3",
        "published_at": "2014-12-13T02:58:44.762Z"
      },
      {
        "number": "4.2.1",
        "published_at": "2015-03-19T16:42:01.191Z"
      },
      {
        "number": "4.2.10",
        "published_at": "2017-09-27T14:29:42.567Z"
      },
      {
        "number": "4.2.10.rc1",
        "published_at": "2017-09-20T19:42:33.297Z"
      },
      {
        "number": "4.2.11",
        "published_at": "2018-11-27T20:07:25.845Z"
      },
      {
        "number": "4.2.11.1",
        "published_at": "2019-03-13T16:37:43.380Z"
      },
      {
        "number": "4.2.11.2",
        "published_at": "2020-05-15T16:30:58.148Z"
      },
      {
        "number": "4.2.11.3",
        "published_at": "2020-05-15T18:35:36.370Z"
      },
      {
        "number": "4.2.1.rc1",
        "published_at": "2015-02-20T22:21:34.214Z"
      },
      {
        "number": "4.2.1.rc2",
        "published_at": "2015-02-25T22:19:50.245Z"
      },
      {
        "number": "4.2.1.rc3",
        "published_at": "2015-03-02T21:35:50.169Z"
      },
      {
        "number": "4.2.1.rc4",
        "published_at": "2015-03-12T21:25:52.551Z"
      },
      {
        "number": "4.2.2",
        "published_at": "2015-06-16T18:03:17.061Z"
      },
      {
        "number": "4.2.3",
        "published_at": "2015-06-25T21:30:57.890Z"
      },
      {
        "number": "4.2.3.rc1",
        "published_at": "2015-06-22T14:23:17.788Z"
      },
      {
        "number": "4.2.4",
        "published_at": "2015-08-24T18:27:12.716Z"
      },
      {
        "number": "4.2.4.rc1",
        "published_at": "2015-08-14T15:21:15.566Z"
      },
      {
        "number": "4.2.5",
        "published_at": "2015-11-12T17:06:55.226Z"
      },
      {
        "number": "4.2.5.1",
        "published_at": "2016-01-25T19:26:41.410Z"
      },
      {
        "number": "4.2.5.2",
        "published_at": "2016-02-29T19:17:10.564Z"
      },
      {
        "number": "4.2.5.rc1",
        "published_at": "2015-10-30T20:47:59.397Z"
      },
      {
        "number": "4.2.5.rc2",
        "published_at": "2015-11-05T03:02:33.340Z"
      },
      {
        "number": "4.2.6",
        "published_at": "2016-03-07T22:33:22.563Z"
      },
      {
        "number": "4.2.6.rc1",
        "published_at": "2016-03-01T18:37:54.172Z"
      },
      {
        "number": "4.2.7",
        "published_at": "2016-07-13T02:57:05.601Z"
      },
      {
        "number": "4.2.7.1",
        "published_at": "2016-08-11T17:35:16.160Z"
      },
      {
        "number": "4.2.7.rc1",
        "published_at": "2016-07-01T00:33:36.424Z"
      },
      {
        "number": "4.2.8",
        "published_at": "2017-02-21T16:08:53.220Z"
      },
      {
        "number": "4.2.8.rc1",
        "published_at": "2017-02-10T02:46:51.222Z"
      },
      {
        "number": "4.2.9",
        "published_at": "2017-06-26T21:30:56.077Z"
      },
      {
        "number": "4.2.9.rc1",
        "published_at": "2017-06-13T18:50:29.897Z"
      },
      {
        "number": "4.2.9.rc2",
        "published_at": "2017-06-19T22:28:22.086Z"
      },
      {
        "number": "5.0.0",
        "published_at": "2016-06-30T21:32:45.255Z"
      },
      {
        "number": "5.0.0.1",
        "published_at": "2016-08-11T17:35:27.196Z"
      },
      {
        "number": "5.0.0.beta1",
        "published_at": "2015-12-18T21:18:13.306Z"
      },
      {
        "number": "5.0.0.beta1.1",
        "published_at": "2016-01-25T19:26:49.903Z"
      },
      {
        "number": "5.0.0.beta2",
        "published_at": "2016-02-01T22:06:25.279Z"
      },
      {
        "number": "5.0.0.beta3",
        "published_at": "2016-02-24T16:16:22.722Z"
      },
      {
        "number": "5.0.0.beta4",
        "published_at": "2016-04-27T20:55:26.508Z"
      },
      {
        "number": "5.0.0.racecar1",
        "published_at": "2016-05-06T22:02:43.345Z"
      },
      {
        "number": "5.0.0.rc1",
        "published_at": "2016-05-06T21:57:46.793Z"
      },
      {
        "number": "5.0.0.rc2",
        "published_at": "2016-06-22T20:03:41.237Z"
      },
      {
        "number": "5.0.1",
        "published_at": "2016-12-21T00:07:46.527Z"
      },
      {
        "number": "5.0.1.rc1",
        "published_at": "2016-11-30T20:02:44.553Z"
      },
      {
        "number": "5.0.1.rc2",
        "published_at": "2016-12-09T19:13:12.953Z"
      },
      {
        "number": "5.0.2",
        "published_at": "2017-03-01T23:13:53.219Z"
      },
      {
        "number": "5.0.2.rc1",
        "published_at": "2017-02-25T00:55:48.618Z"
      },
      {
        "number": "5.0.3",
        "published_at": "2017-05-12T20:08:33.226Z"
      },
      {
        "number": "5.0.4",
        "published_at": "2017-06-19T21:58:56.501Z"
      },
      {
        "number": "5.0.4.rc1",
        "published_at": "2017-06-14T20:49:29.610Z"
      },
      {
        "number": "5.0.5",
        "published_at": "2017-07-31T19:05:29.060Z"
      },
      {
        "number": "5.0.5.rc1",
        "published_at": "2017-07-19T19:43:58.280Z"
      },
      {
        "number": "5.0.5.rc2",
        "published_at": "2017-07-25T20:26:10.369Z"
      },
      {
        "number": "5.0.6",
        "published_at": "2017-09-08T00:47:42.201Z"
      },
      {
        "number": "5.0.6.rc1",
        "published_at": "2017-08-24T19:21:20.599Z"
      },
      {
        "number": "5.0.7",
        "published_at": "2018-03-29T18:18:14.388Z"
      },
      {
        "number": "5.0.7.1",
        "published_at": "2018-11-27T20:09:36.347Z"
      },
      {
        "number": "5.0.7.2",
        "published_at": "2019-03-13T16:44:05.926Z"
      },
      {
        "number": "5.1.0",
        "published_at": "2017-04-27T21:00:47.670Z"
      },
      {
        "number": "5.1.0.beta1",
        "published_at": "2017-02-23T20:00:44.720Z"
      },
      {
        "number": "5.1.0.rc1",
        "published_at": "2017-03-20T18:57:56.595Z"
      },
      {
        "number": "5.1.0.rc2",
        "published_at": "2017-04-21T01:31:13.442Z"
      },
      {
        "number": "5.1.1",
        "published_at": "2017-05-12T20:11:39.743Z"
      },
      {
        "number": "5.1.2",
        "published_at": "2017-06-26T21:51:41.161Z"
      },
      {
        "number": "5.1.2.rc1",
        "published_at": "2017-06-20T17:03:49.322Z"
      },
      {
        "number": "5.1.3",
        "published_at": "2017-08-03T19:15:15.370Z"
      },
      {
        "number": "5.1.3.rc1",
        "published_at": "2017-07-19T19:38:05.393Z"
      },
      {
        "number": "5.1.3.rc2",
        "published_at": "2017-07-25T20:18:18.420Z"
      },
      {
        "number": "5.1.3.rc3",
        "published_at": "2017-07-31T19:12:53.241Z"
      },
      {
        "number": "5.1.4",
        "published_at": "2017-09-08T00:52:07.791Z"
      },
      {
        "number": "5.1.4.rc1",
        "published_at": "2017-08-24T19:37:37.728Z"
      },
      {
        "number": "5.1.5",
        "published_at": "2018-02-14T20:02:02.541Z"
      },
      {
        "number": "5.1.5.rc1",
        "published_at": "2018-02-01T19:00:37.520Z"
      },
      {
        "number": "5.1.6",
        "published_at": "2018-03-29T18:29:03.149Z"
      },
      {
        "number": "5.1.6.1",
        "published_at": "2018-11-27T20:11:47.585Z"
      },
      {
        "number": "5.1.6.2",
        "published_at": "2019-03-13T16:46:09.784Z"
      },
      {
        "number": "5.1.7",
        "published_at": "2019-03-28T02:48:31.504Z"
      },
      {
        "number": "5.1.7.rc1",
        "published_at": "2019-03-22T04:13:43.625Z"
      },
      {
        "number": "5.2.0",
        "published_at": "2018-04-09T20:07:04.834Z"
      },
      {
        "number": "5.2.0.beta1",
        "published_at": "2017-11-27T19:19:13.809Z"
      },
      {
        "number": "5.2.0.beta2",
        "published_at": "2017-11-28T05:04:37.765Z"
      },
      {
        "number": "5.2.0.rc1",
        "published_at": "2018-01-30T23:38:56.843Z"
      },
      {
        "number": "5.2.0.rc2",
        "published_at": "2018-03-20T17:54:58.165Z"
      },
      {
        "number": "5.2.1",
        "published_at": "2018-08-07T21:44:52.020Z"
      },
      {
        "number": "5.2.1.1",
        "published_at": "2018-11-27T20:14:16.796Z"
      },
      {
        "number": "5.2.1.rc1",
        "published_at": "2018-07-30T20:22:38.749Z"
      },
      {
        "number": "5.2.2",
        "published_at": "2018-12-04T18:15:02.233Z"
      },
      {
        "number": "5.2.2.1",
        "published_at": "2019-03-13T16:54:48.659Z"
      },
      {
        "number": "5.2.2.rc1",
        "published_at": "2018-11-28T22:55:23.827Z"
      },
      {
        "number": "5.2.3",
        "published_at": "2019-03-28T03:02:40.948Z"
      },
      {
        "number": "5.2.3.rc1",
        "published_at": "2019-03-22T03:35:22.787Z"
      },
      {
        "number": "5.2.4",
        "published_at": "2019-11-27T15:48:34.344Z"
      },
      {
        "number": "5.2.4.1",
        "published_at": "2019-12-18T19:04:02.693Z"
      },
      {
        "number": "5.2.4.2",
        "published_at": "2020-03-19T16:38:05.377Z"
      },
      {
        "number": "5.2.4.3",
        "published_at": "2020-05-18T15:43:14.514Z"
      },
      {
        "number": "5.2.4.4",
        "published_at": "2020-09-09T18:40:04.077Z"
      },
      {
        "number": "5.2.4.5",
        "published_at": "2021-02-10T20:36:47.152Z"
      },
      {
        "number": "5.2.4.6",
        "published_at": "2021-05-05T15:29:45.600Z"
      },
      {
        "number": "5.2.4.rc1",
        "published_at": "2019-11-23T00:29:26.852Z"
      },
      {
        "number": "5.2.5",
        "published_at": "2021-03-26T17:21:08.771Z"
      },
      {
        "number": "5.2.6",
        "published_at": "2021-05-05T17:09:14.109Z"
      },
      {
        "number": "5.2.6.1",
        "published_at": "2022-02-11T18:44:20.296Z"
      },
      {
        "number": "5.2.6.2",
        "published_at": "2022-02-11T19:37:40.891Z"
      },
      {
        "number": "5.2.6.3",
        "published_at": "2022-03-08T17:46:06.998Z"
      },
      {
        "number": "5.2.7",
        "published_at": "2022-03-11T00:01:27.495Z"
      },
      {
        "number": "5.2.7.1",
        "published_at": "2022-04-26T19:23:33.623Z"
      },
      {
        "number": "5.2.8",
        "published_at": "2022-05-09T14:04:34.858Z"
      },
      {
        "number": "5.2.8.1",
        "published_at": "2022-07-12T17:26:29.354Z"
      },
      {
        "number": "6.0.0",
        "published_at": "2019-08-16T18:01:50.039Z"
      },
      {
        "number": "6.0.0.beta1",
        "published_at": "2019-01-18T21:24:30.197Z"
      },
      {
        "number": "6.0.0.beta2",
        "published_at": "2019-02-25T22:46:20.904Z"
      },
      {
        "number": "6.0.0.beta3",
        "published_at": "2019-03-13T17:03:33.751Z"
      },
      {
        "number": "6.0.0.rc1",
        "published_at": "2019-04-24T18:51:47.763Z"
      },
      {
        "number": "6.0.0.rc2",
        "published_at": "2019-07-22T21:13:05.492Z"
      },
      {
        "number": "6.0.1",
        "published_at": "2019-11-05T14:41:11.133Z"
      },
      {
        "number": "6.0.1.rc1",
        "published_at": "2019-10-31T20:12:42.982Z"
      },
      {
        "number": "6.0.2",
        "published_at": "2019-12-13T18:22:04.637Z"
      },
      {
        "number": "6.0.2.1",
        "published_at": "2019-12-18T19:09:59.411Z"
      },
      {
        "number": "6.0.2.2",
        "published_at": "2020-03-19T16:44:43.643Z"
      },
      {
        "number": "6.0.2.rc1",
        "published_at": "2019-11-27T15:14:18.533Z"
      },
      {
        "number": "6.0.2.rc2",
        "published_at": "2019-12-09T16:14:24.296Z"
      },
      {
        "number": "6.0.3",
        "published_at": "2020-05-06T18:06:19.228Z"
      },
      {
        "number": "6.0.3.1",
        "published_at": "2020-05-18T15:47:58.979Z"
      },
      {
        "number": "6.0.3.2",
        "published_at": "2020-06-17T14:55:22.792Z"
      },
      {
        "number": "6.0.3.3",
        "published_at": "2020-09-09T18:40:40.945Z"
      },
      {
        "number": "6.0.3.4",
        "published_at": "2020-10-07T16:51:45.423Z"
      },
      {
        "number": "6.0.3.5",
        "published_at": "2021-02-10T20:40:58.384Z"
      },
      {
        "number": "6.0.3.6",
        "published_at": "2021-03-26T17:34:24.038Z"
      },
      {
        "number": "6.0.3.7",
        "published_at": "2021-05-05T16:02:39.448Z"
      },
      {
        "number": "6.0.3.rc1",
        "published_at": "2020-05-01T17:19:16.854Z"
      },
      {
        "number": "6.0.4",
        "published_at": "2021-06-15T20:18:42.690Z"
      },
      {
        "number": "6.0.4.1",
        "published_at": "2021-08-19T16:24:04.111Z"
      },
      {
        "number": "6.0.4.2",
        "published_at": "2021-12-14T20:11:34.368Z"
      },
      {
        "number": "6.0.4.3",
        "published_at": "2021-12-14T23:01:12.359Z"
      },
      {
        "number": "6.0.4.4",
        "published_at": "2021-12-15T22:48:07.861Z"
      },
      {
        "number": "6.0.4.5",
        "published_at": "2022-02-11T18:25:30.490Z"
      },
      {
        "number": "6.0.4.6",
        "published_at": "2022-02-11T19:40:17.360Z"
      },
      {
        "number": "6.0.4.7",
        "published_at": "2022-03-08T17:47:49.182Z"
      },
      {
        "number": "6.0.4.8",
        "published_at": "2022-04-26T19:27:17.853Z"
      },
      {
        "number": "6.0.5",
        "published_at": "2022-05-09T13:55:52.176Z"
      },
      {
        "number": "6.0.5.1",
        "published_at": "2022-07-12T17:28:31.607Z"
      },
      {
        "number": "6.0.6",
        "published_at": "2022-09-09T18:32:26.797Z"
      },
      {
        "number": "6.0.6.1",
        "published_at": "2023-01-17T18:53:31.767Z"
      },
      {
        "number": "6.1.0",
        "published_at": "2020-12-09T19:58:25.767Z"
      },
      {
        "number": "6.1.0.rc1",
        "published_at": "2020-11-02T21:21:17.693Z"
      },
      {
        "number": "6.1.0.rc2",
        "published_at": "2020-12-01T22:02:12.587Z"
      },
      {
        "number": "6.1.1",
        "published_at": "2021-01-07T23:00:39.767Z"
      },
      {
        "number": "6.1.2",
        "published_at": "2021-02-09T21:30:59.508Z"
      },
      {
        "number": "6.1.2.1",
        "published_at": "2021-02-10T20:46:54.673Z"
      },
      {
        "number": "6.1.3",
        "published_at": "2021-02-17T18:43:25.690Z"
      },
      {
        "number": "6.1.3.1",
        "published_at": "2021-03-26T18:08:37.957Z"
      },
      {
        "number": "6.1.3.2",
        "published_at": "2021-05-05T15:47:12.170Z"
      },
      {
        "number": "6.1.4",
        "published_at": "2021-06-24T20:41:36.150Z"
      },
      {
        "number": "6.1.4.1",
        "published_at": "2021-08-19T16:27:05.901Z"
      },
      {
        "number": "6.1.4.2",
        "published_at": "2021-12-14T19:54:23.219Z"
      },
      {
        "number": "6.1.4.3",
        "published_at": "2021-12-14T23:02:52.630Z"
      },
      {
        "number": "6.1.4.4",
        "published_at": "2021-12-15T22:54:43.260Z"
      },
      {
        "number": "6.1.4.5",
        "published_at": "2022-02-11T18:23:00.402Z"
      },
      {
        "number": "6.1.4.6",
        "published_at": "2022-02-11T19:42:12.433Z"
      },
      {
        "number": "6.1.4.7",
        "published_at": "2022-03-08T17:49:05.430Z"
      },
      {
        "number": "6.1.5",
        "published_at": "2022-03-10T21:17:17.197Z"
      },
      {
        "number": "6.1.5.1",
        "published_at": "2022-04-26T19:30:41.477Z"
      },
      {
        "number": "6.1.6",
        "published_at": "2022-05-09T13:46:43.653Z"
      },
      {
        "number": "6.1.6.1",
        "published_at": "2022-07-12T17:29:59.374Z"
      },
      {
        "number": "6.1.7",
        "published_at": "2022-09-09T18:39:14.216Z"
      },
      {
        "number": "6.1.7.1",
        "published_at": "2023-01-17T18:54:36.574Z"
      },
      {
        "number": "6.1.7.2",
        "published_at": "2023-01-25T03:23:38.693Z"
      },
      {
        "number": "6.1.7.3",
        "published_at": "2023-03-13T18:48:59.163Z"
      },
      {
        "number": "6.1.7.4",
        "published_at": "2023-06-26T21:32:09.757Z"
      },
      {
        "number": "6.1.7.5",
        "published_at": "2023-08-22T17:16:21.814Z"
      },
      {
        "number": "6.1.7.6",
        "published_at": "2023-08-22T20:08:17.323Z"
      },
      {
        "number": "7.0.0",
        "published_at": "2021-12-15T23:45:57.959Z"
      },
      {
        "number": "7.0.0.alpha1",
        "published_at": "2021-09-15T21:58:08.365Z"
      },
      {
        "number": "7.0.0.alpha2",
        "published_at": "2021-09-15T23:16:26.580Z"
      },
      {
        "number": "7.0.0.rc1",
        "published_at": "2021-12-06T21:33:14.325Z"
      },
      {
        "number": "7.0.0.rc2",
        "published_at": "2021-12-14T19:40:58.273Z"
      },
      {
        "number": "7.0.0.rc3",
        "published_at": "2021-12-14T23:04:49.725Z"
      },
      {
        "number": "7.0.1",
        "published_at": "2022-01-06T21:55:27.202Z"
      },
      {
        "number": "7.0.2",
        "published_at": "2022-02-08T23:13:21.486Z"
      },
      {
        "number": "7.0.2.1",
        "published_at": "2022-02-11T18:19:21.363Z"
      },
      {
        "number": "7.0.2.2",
        "published_at": "2022-02-11T19:44:19.017Z"
      },
      {
        "number": "7.0.2.3",
        "published_at": "2022-03-08T17:50:52.496Z"
      },
      {
        "number": "7.0.2.4",
        "published_at": "2022-04-26T19:33:25.138Z"
      },
      {
        "number": "7.0.3",
        "published_at": "2022-05-09T13:41:57.714Z"
      },
      {
        "number": "7.0.3.1",
        "published_at": "2022-07-12T17:31:41.727Z"
      },
      {
        "number": "7.0.4",
        "published_at": "2022-09-09T18:42:56.698Z"
      },
      {
        "number": "7.0.4.1",
        "published_at": "2023-01-17T18:55:33.129Z"
      },
      {
        "number": "7.0.4.2",
        "published_at": "2023-01-25T03:14:29.671Z"
      },
      {
        "number": "7.0.4.3",
        "published_at": "2023-03-13T18:53:43.517Z"
      },
      {
        "number": "7.0.5",
        "published_at": "2023-05-24T19:21:28.229Z"
      },
      {
        "number": "7.0.5.1",
        "published_at": "2023-06-26T21:43:05.607Z"
      },
      {
        "number": "7.0.6",
        "published_at": "2023-06-29T20:57:24.359Z"
      },
      {
        "number": "7.0.7",
        "published_at": "2023-08-09T23:58:21.715Z"
      },
      {
        "number": "7.0.7.1",
        "published_at": "2023-08-22T17:20:56.278Z"
      },
      {
        "number": "7.0.7.2",
        "published_at": "2023-08-22T20:10:49.614Z"
      },
      {
        "number": "7.0.8",
        "published_at": "2023-09-09T19:15:48.031Z"
      },
      {
        "number": "7.1.0",
        "published_at": "2023-10-05T08:09:44.611Z"
      },
      {
        "number": "7.1.0.beta1",
        "published_at": "2023-09-13T00:41:49.913Z"
      },
      {
        "number": "7.1.0.rc1",
        "published_at": "2023-09-27T04:03:48.642Z"
      },
      {
        "number": "7.1.0.rc2",
        "published_at": "2023-10-01T22:02:22.755Z"
      },
      {
        "number": "7.1.1",
        "published_at": "2023-10-11T22:19:27.155Z"
      },
      {
        "number": "7.1.2",
        "published_at": "2023-11-10T21:52:10.943Z"
      }
    ]
  }
}
```
