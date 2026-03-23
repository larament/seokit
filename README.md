![A Clean SEO Toolkit for Laravel](assets/cover.svg)

# Technical SEO made easy in Laravel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/larament/seokit/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/larament/seokit/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)
[![License](https://img.shields.io/packagist/l/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)

SeoKit is a Laravel SEO toolkit for managing meta tags, Open Graph data, Twitter cards, and JSON-LD from one consistent API.

It is designed for applications that need a practical SEO layer without stitching together multiple packages or scattering metadata logic across controllers, models, and views.

## Highlights

- Complete SEO coverage for meta tags, Open Graph, Twitter cards, and JSON-LD structured data
- Clean Laravel integration through a facade, Blade directive, install command, and model traits
- Flexible data flow with support for computed SEO data and database-backed SEO records
- Polymorphic model SEO support for managing metadata separately from your domain models
- Built-in caching and sensible defaults to reduce repetitive setup and unnecessary queries
- Designed for modern Laravel applications, with automated test coverage for Laravel 11, 12, and 13

## Requirements

- PHP 8.3+
- Laravel 11+

## Installation

Install the package with Composer:

```bash
composer require larament/seokit
```

Publish the package assets with the installer command:

```bash
php artisan seokit:install
```

If you prefer to publish assets manually:

```bash
php artisan vendor:publish --tag="seokit-config"
php artisan vendor:publish --tag="seokit-migrations"
php artisan migrate
```

## Quick Start

Set page metadata in your controller:

```php
use Larament\SeoKit\Facades\SeoKit;

public function show(Post $post)
{
    SeoKit::title($post->title)
        ->description($post->excerpt)
        ->image($post->featured_image)
        ->canonical(route('posts.show', $post));

    return view('posts.show', compact('post'));
}
```

Render the tags in your layout:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @seoKit

    <!-- ... -->
</head>
<body>
    <!-- ... -->
</body>
</html>
```

For model-driven SEO, SeoKit also provides `HasSeo` and `HasSeoData` traits.

## Documentation

Detailed documentation lives in the GitHub wiki:

- [Getting Started](../../seokit/wiki)
- [Core Concepts](../../seokit/wiki/Core-Concepts)
- [Advanced SEO Management](../../seokit/wiki/Advanced-SEO-Management)
- [Database-Backed SEO](../../seokit/wiki/Database-Backed-SEO)
- [Cookbooks and Examples](../../seokit/wiki/Cookbooks-and-Examples)

The README is intentionally kept short. The wiki should be the source of truth for package guides, examples, and feature-specific documentation.

## Development

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
composer analyse
```

Format the codebase:

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome. Please open an issue or submit a pull request if you want to improve the package.

## Security

Please review [the security policy](../../security/policy) for reporting vulnerabilities.

## Credits

- [Raziul Islam](https://github.com/iRaziul)
- [All Contributors](../../contributors)

## License

SeoKit is open-sourced software licensed under the [MIT license](LICENSE.md).
