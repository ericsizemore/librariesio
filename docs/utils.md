# Utils

`Esi\LibrariesIO\Utils`

Utility class to provide useful methods used throughout the library. The methods that you will most likely make use of most are `raw()`, `toArray()`, and `toObject()`.


```php
// Public methods
endpointParameters(string $endpoint, string $subset, array $options): array;
normalizeEndpoint(string | null $endpoint, string $apiUrl): string;
normalizeMethod(string $method): string;
raw(ResponseInterface $response): string;
searchAdditionalParams(array $options): array;
searchVerifySortOption(string $sort): string;
toArray(ResponseInterface $response): array;
toObject(ResponseInterface $response): stdClass;
validateCachePath(?string $cachePath = null): ?string;
validatePagination(array $options): array;

// Private methods
processEndpointFormat(string $format, array $options): string;
```

*** Documentation WIP ***
