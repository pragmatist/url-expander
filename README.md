# URL Expander

[![Build Status](https://travis-ci.org/pragmatist/url-expander.svg)](https://travis-ci.org/pragmatist/url-expander)
[![Build Status](https://scrutinizer-ci.com/g/pragmatist/url-expander/badges/build.png?b=master)](https://scrutinizer-ci.com/g/pragmatist/url-expander/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pragmatist/url-expander/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pragmatist/url-expander/?branch=master)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/pragmatist/url-expander/blob/master/LICENSE)

The `UrlExpander` package provides a simple library to expand short URLs.

## System Requirements

You need **PHP >= 7.0** to use this library.

## Installation

Install `UrlExpander` using Composer.

```bash
$ composer require pragmatist/url-expander
```

## Usage

Simple redirect-based URL expansion:

```php
<?php

use Pragmatist\UrlExpander\RedirectBasedUrlExpander;
use League\Uri\Schemes\Http;

$expander = RedirectBasedUrlExpander::createWithGuzzleClient();
$expandedUri = $expander->expand(
    Http::createFromString('http://bit.ly/2dtGsBS')
);

echo $expandedUri; // Outputs: https://github.com/pragmatist/url-expander
```

Use the CachingUrlExpander to cache expanded URLs in a PSR-6 compatible cache:

```php
<?php

use Cache\Adapter\PHPArray\ArrayCachePool;
use Pragmatist\UrlExpander\CachingUrlExpander;
use Pragmatist\UrlExpander\RedirectBasedUrlExpander;
use League\Uri\Schemes\Http;

$expander = new CachingUrlExpander(
    RedirectBasedUrlExpander::createWithGuzzleClient(),
    new ArrayCachePool(); // From the cache/array-adapter package
);

$shortUri = Http::createFromString('http://bit.ly/2dtGsBS');
echo $expander->expand($shortUri); // Outputs: https://github.com/pragmatist/url-expander
echo $expander->expand($shortUri); // From cache
```

You can also use the command line client to expand short URLs:

```bash
$ ./bin/expand http://bit.ly/2dtGsBS
https://github.com/pragmatist/url-expander
```

## Testing

`UrlExpander` has a [PHPUnit](https://phpunit.de/) test suite. To run the tests, run the following command from the project folder.

```bash
$ ./vendor/bin/phpunit
```

## Security

If you discover any security related issues, please email hello@pragmatist.nl instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
