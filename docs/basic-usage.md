# Basic usage

LibrariesIO is a simple API wrapper/client for the Libraries.io API. Since this class solely relies on the libraries.io API, you will need an api key from the libraries.io API service. Signing up for an account is free. You can find your api key in your [account](https://libraries.io/account).

## Import the library

```php
use Esi\LibrariesIO\LibrariesIO;
```

A `Utils` class is also available which can take the API response and covert it to an array, object, or give you the raw json data. All methods of `Utils` are static. To use `Utils`, import it using:

```php
use Esi\LibrariesIO\Utils;
```

## Available Endpoints

  * [Platform](platform.md)
  * [Project](project.md)
  * [Repository](repository.md)
  * [Subscription](subscription.md)
  * [User](user.md)

## Platform Example

The Esi\LibrariesIO\LibrariesIO::platform() function, for example, provides the ability to access the 'platform' API endpoint to gather all platforms the libraries.io service supports.

See more: [Platform](platform.md)

```php
use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;

$api = new LibrariesIO('..yourapikey..', \sys_get_temp_dir());
$response = $api->platform();

print_r(Utils::raw($response));
```

Would return something like:

```json
[
  {
    "name": "NPM",
    "project_count": 4081204,
    "homepage": "https://www.npmjs.com",
    "color": "#f1e05a",
    "default_language": "JavaScript"
  },
  {
    "name": "Maven",
    "project_count": 588275,
    "homepage": "http://maven.org",
    "color": "#b07219",
    "default_language": "Java"
  },
  {
    "name": "Pypi",
    "project_count": 499320,
    "homepage": "https://pypi.org/",
    "color": "#3572A5",
    "default_language": "Python"
  },
  {
    "name": "NuGet",
    "project_count": 487449,
    "homepage": "https://www.nuget.org",
    "color": "#178600",
    "default_language": "C#"
  },
  {
    "name": "Go",
    "project_count": 484898,
    "homepage": "https://pkg.go.dev",
    "color": "#375eab",
    "default_language": null
  },
  {
    "name": "Packagist",
    "project_count": 405922,
    "homepage": "https://packagist.org",
    "color": "#4F5D95",
    "default_language": "PHP"
  },
  {
    "name": "Rubygems",
    "project_count": 183149,
    "homepage": "https://rubygems.org",
    "color": "#701516",
    "default_language": "Ruby"
  },
  {
    "name": "Cargo",
    "project_count": 135799,
    "homepage": "https://crates.io",
    "color": "#dea584",
    "default_language": "Rust"
  },
  {
    "name": "CocoaPods",
    "project_count": 96000,
    "homepage": "http://cocoapods.org/",
    "color": "#438eff",
    "default_language": "Objective-C"
  },
  {
    "name": "Bower",
    "project_count": 69215,
    "homepage": "http://bower.io",
    "color": "#563d7c",
    "default_language": "CSS"
  },
  {
    "name": "Pub",
    "project_count": 48925,
    "homepage": "https://pub.dartlang.org",
    "color": "#00B4AB",
    "default_language": "Dart"
  },
  {
    "name": "CPAN",
    "project_count": 40509,
    "homepage": "https://metacpan.org",
    "color": "#0298c3",
    "default_language": "Perl"
  },
  {
    "name": "CRAN",
    "project_count": 25484,
    "homepage": "https://cran.r-project.org/",
    "color": "#198CE7",
    "default_language": "R"
  },
  {
    "name": "Clojars",
    "project_count": 24189,
    "homepage": "https://clojars.org",
    "color": "#db5855",
    "default_language": "Clojure"
  },
  {
    "name": "Conda",
    "project_count": 18850,
    "homepage": "https://anaconda.org",
    "color": "#fff",
    "default_language": null
  },
  {
    "name": "Hackage",
    "project_count": 17609,
    "homepage": "http://hackage.haskell.org",
    "color": "#29b544",
    "default_language": null
  },
  {
    "name": "Hex",
    "project_count": 15420,
    "homepage": "https://hex.pm",
    "color": "#6e4a7e",
    "default_language": "Elixir"
  },
  {
    "name": "Meteor",
    "project_count": 13342,
    "homepage": "https://atmospherejs.com",
    "color": "#f1e05a",
    "default_language": "JavaScript"
  },
  {
    "name": "Homebrew",
    "project_count": 8714,
    "homepage": "http://brew.sh/",
    "color": "#555555",
    "default_language": "C"
  },
  {
    "name": "Puppet",
    "project_count": 6920,
    "homepage": "https://forge.puppet.com",
    "color": "#302B6D",
    "default_language": "Puppet"
  },
  {
    "name": "Carthage",
    "project_count": 4679,
    "homepage": "https://github.com/Carthage/Carthage",
    "color": "#ffac45",
    "default_language": "Swift"
  },
  {
    "name": "SwiftPM",
    "project_count": 4207,
    "homepage": "https://developer.apple.com/swift/",
    "color": "#ffac45",
    "default_language": "Swift"
  },
  {
    "name": "Julia",
    "project_count": 3042,
    "homepage": "http://pkg.julialang.org/",
    "color": "#a270ba",
    "default_language": "Julia"
  },
  {
    "name": "Elm",
    "project_count": 2892,
    "homepage": "http://package.elm-lang.org/",
    "color": "#60B5CC",
    "default_language": "Elm"
  },
  {
    "name": "Dub",
    "project_count": 2705,
    "homepage": "http://code.dlang.org",
    "color": "#ba595e",
    "default_language": "D"
  },
  {
    "name": "Racket",
    "project_count": 2554,
    "homepage": "http://pkgs.racket-lang.org/",
    "color": "#375eab",
    "default_language": null
  },
  {
    "name": "Nimble",
    "project_count": 2354,
    "homepage": "https://github.com/nim-lang/nimble",
    "color": "#37775b",
    "default_language": "Nim"
  },
  {
    "name": "Haxelib",
    "project_count": 1703,
    "homepage": "https://lib.haxe.org",
    "color": "#df7900",
    "default_language": "Haxe"
  },
  {
    "name": "PureScript",
    "project_count": 747,
    "homepage": "https://github.com/purescript/psc-package",
    "color": "#1D222D",
    "default_language": "PureScript"
  },
  {
    "name": "Alcatraz",
    "project_count": 462,
    "homepage": "http://alcatraz.io",
    "color": "#438eff",
    "default_language": "Objective-C"
  },
  {
    "name": "Inqlude",
    "project_count": 228,
    "homepage": "https://inqlude.org/",
    "color": "#f34b7d",
    "default_language": "C++"
  }
]
```
