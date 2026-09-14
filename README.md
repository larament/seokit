![A Clean SEO Toolkit for Laravel](assets/cover.svg)

# Technical SEO made easy in Laravel.

[![Laravel Compatibility](https://badge.laravel.cloud/badge/larament/seokit?style=for-the-badge)](https://packagist.org/packages/larament/seokit)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/larament/seokit.svg?style=for-the-badge)](https://packagist.org/packages/larament/seokit)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/larament/seokit/run-tests.yml?branch=main&label=tests&style=for-the-badge)](https://github.com/larament/seokit/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/larament/seokit.svg?style=for-the-badge)](https://packagist.org/packages/larament/seokit)
[![License](https://img.shields.io/packagist/l/larament/seokit.svg?style=for-the-badge)](https://packagist.org/packages/larament/seokit)

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

Visit the official documentation website for complete guides, architecture details, and cookbook examples:

👉 **[SeoKit Documentation](https://larament.github.io/seokit/)**

- [Introduction & Architecture](docs/getting-started/introduction.md)
- [Installation & Setup](docs/getting-started/installation.md)
- [Configuration Reference](docs/getting-started/configuration.md)
- [Meta Tags & Titles](docs/guide/meta-tags.md)
- [Open Graph & Social Sharing](docs/guide/open-graph.md)
- [Twitter Cards](docs/guide/twitter-cards.md)
- [JSON-LD Structured Data](docs/guide/json-ld.md)
- [Blade Directives](docs/guide/blade-directives.md)
- [Database-Backed SEO (`HasSeo`)](docs/models/database-backed-seo.md)
- [Computed SEO Data (`HasSeoData`)](docs/models/computed-seo.md)
- [Images & Storage Disks](docs/models/images-and-storage.md)
- [Macros & Extensions](docs/advanced/macros-and-customization.md)
- [Caching & Performance](docs/advanced/caching.md)
- [Laravel Octane Safety](docs/advanced/octane.md)

To run the documentation website locally:

```bash
npm run docs:dev
# or
bun run docs:dev
```

## Development

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
composer analyse
```

Lint and format the codebase:

```bash
composer lint
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
