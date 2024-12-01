# Installation

Installing Esi\LibrariesIO is very easy, if you're using [composer](http://getcomposer.com). 
If you haven't done so, install composer, and use **composer require** to install Esi\LibrariesIO.

```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar require esi/librariesio
```

## First usage

Make sure you include `vendor/autoload.php` in your application. To make all of LibrariesIO's components available at once:

```php
use Esi\LibrariesIO\LibrariesIO;
use Esi\LibrariesIO\Utils;
```

For more information check out the [basic-usage](basic-usage.md) documenation. Further reading:

* Endpoints:
  * [Platform](platform.md)
  * [Project](project.md)
  * [Repository](repository.md)
  * [Subscription](subscription.md)
  * [User](user.md)

* Extras:
  * [Utils](utils.md)
