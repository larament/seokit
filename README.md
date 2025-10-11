# SeoKit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/larament/seokit/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/larament/seokit/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)
[![License](https://img.shields.io/packagist/l/larament/seokit.svg?style=flat-square)](https://packagist.org/packages/larament/seokit)

A complete SEO package for Laravel, covering everything from meta tags to social sharing and structured data.

## Introduction

**SeoKit** is a comprehensive SEO solution for Laravel applications that makes it easy to manage all aspects of your site's search engine optimization. Whether you need basic meta tags, rich social media previews, or complex structured data markup, SeoKit has you covered.

### Why SeoKit?

-   **Complete SEO Solution**: Meta tags, Open Graph, Twitter Cards, and JSON-LD structured data in one package
-   **Database-backed**: Store SEO data in your database with polymorphic relationships
-   **Model Integration**: Simple traits to add SEO capabilities to any Eloquent model
-   **Flexible Configuration**: Sensible defaults with extensive customization options
-   **Performance**: Built-in caching for SEO data to minimize database queries
-   **Developer Friendly**: Clean, fluent API with chainable methods
-   **Modern Laravel**: Built for Laravel 11.x and 12.x with PHP 8.3+

### Features

-   🏷️ **Meta Tags Management** - Title, description, keywords, robots, canonical URLs and more
-   🌐 **Open Graph Protocol** - Full support for Facebook and social sharing
-   🐦 **Twitter Cards** - Summary, large image, player, and app cards
-   📊 **JSON-LD Structured Data** - Schema.org markup for rich search results
-   💾 **Database-backed SEO** - Store SEO data per model instance
-   🎭 **Model Traits** - Easy integration with Eloquent models
-   ⚡ **Caching** - Automatic caching of database SEO data for better performance
-   🎨 **Blade Directive** - Simple `@seoKit` directive for rendering
-   🔧 **Highly Configurable** - Extensive configuration options

## Requirements

-   PHP 8.3 or higher
-   Laravel 11.x or 12.x

## Installation

You can install the package via composer:

```bash
composer require larament/seokit
```

### Quick Installation

The package comes with an install command that will publish the config file, migrations, and optionally run the migrations:

```bash
php artisan seokit:install
```

### Manual Installation

Alternatively, you can publish the config file and migrations manually:

```bash
php artisan vendor:publish --tag="seokit-config"
php artisan vendor:publish --tag="seokit-migrations"
php artisan migrate
```

## Configuration

The configuration file `config/seokit.php` provides extensive options for customizing the package behavior:

```php
return [
    // Database table name
    'table_name' => 'seokit',

    // Auto-generate title from URL when not set
    'auto_title_from_url' => true,

    // Custom title inference callback
    'title_inference_callback' => null,

    // Default meta tags
    'defaults' => [
        'title' => 'My Laravel App',
        'before_title' => null,
        'after_title' => null,
        'title_separator' => ' - ',
        'description' => null,
        'canonical' => null, // null = current URL, 'full' = full URL, false = disabled
        'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
    ],

    // Open Graph settings
    'opengraph' => [
        'enabled' => true,
        'defaults' => [
            'site_name' => env('APP_NAME', 'Laravel'),
            'type' => 'website',
            'url' => null,
            'locale' => 'en_US',
        ],
    ],

    // Twitter Card settings
    'twitter' => [
        'enabled' => true,
        'defaults' => [
            'card' => 'summary_large_image',
            'site' => '@yourusername',
            'creator' => '@yourusername',
        ],
    ],

    // JSON-LD settings
    'json_ld' => [
        'enabled' => true,
        'defaults' => [],
    ],
];
```

## Basic Usage

### Simple Usage

The easiest way to set SEO tags is using the `SeoKit` facade:

```php
use Larament\SeoKit\Facades\SeoKit;

// In your controller
public function show(Post $post)
{
    SeoKit::title($post->title)
        ->description($post->excerpt)
        ->image($post->featured_image);

    return view('posts.show', compact('post'));
}
```

Then in your layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html>
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

The `@seoKit` directive will render all configured SEO tags including meta tags, Open Graph, Twitter Cards, and JSON-LD.

### Accessing Individual Components

You can also work with individual components for more control:

```php
use Larament\SeoKit\Facades\SeoKit;

// Meta tags
SeoKit::meta()
    ->title('My Page Title')
    ->description('A great description')
    ->keywords(['laravel', 'seo', 'optimization'])
    ->canonical('https://example.com/page');

// Open Graph
SeoKit::opengraph()
    ->title('My Page Title')
    ->description('A great description')
    ->image('https://example.com/image.jpg')
    ->type('article');

// Twitter Cards
SeoKit::twitter()
    ->card('summary_large_image')
    ->title('My Page Title')
    ->description('A great description')
    ->image('https://example.com/image.jpg');

// JSON-LD
SeoKit::jsonld()
    ->article([
        'headline' => 'My Article',
        'description' => 'Article description',
        'author' => [
            '@type' => 'Person',
            'name' => 'John Doe',
        ],
    ]);
```

## Advanced Usage

### Meta Tags

#### Custom Meta Tags

Add any custom meta tag:

```php
SeoKit::meta()->addMeta('author', 'John Doe');
SeoKit::meta()->addMeta('theme-color', '#ffffff');
```

#### Robots Meta Tag

Control how search engines index your pages:

```php
// String format
SeoKit::meta()->robots('noindex, nofollow');

// Array format
SeoKit::meta()->robots(['noindex', 'nofollow', 'noarchive']);
```

#### Canonical URLs

Specify the canonical URL for duplicate content:

```php
SeoKit::meta()->canonical('https://example.com/canonical-page');
```

#### Language Alternates

Add alternate language versions of your page:

```php
SeoKit::meta()
    ->addLanguage('en', 'https://example.com/en/page')
    ->addLanguage('es', 'https://example.com/es/page')
    ->addLanguage('fr', 'https://example.com/fr/page');
```

#### Pagination

For paginated content, add prev/next links:

```php
SeoKit::meta()
    ->prev('https://example.com/page/1', condition: $currentPage > 1)
    ->next('https://example.com/page/3', condition: $currentPage < $totalPages);
```

### Open Graph

#### Article Metadata

For blog posts and articles:

```php
SeoKit::opengraph()->article(
    publishedTime: '2024-01-15T08:00:00+00:00',
    modifiedTime: '2024-01-16T10:30:00+00:00',
    authors: ['https://example.com/author/john-doe'],
    section: 'Technology',
    tags: ['Laravel', 'PHP', 'SEO']
);
```

#### Video Content

For video content:

```php
// Video movie
SeoKit::opengraph()->videoMovie(
    actor: ['https://example.com/actor/john'],
    director: ['https://example.com/director/jane'],
    duration: 7200,
    releaseDate: '2024-01-01',
    tag: ['action', 'adventure']
);

// Video episode
SeoKit::opengraph()->videoEpisode(
    series: 'https://example.com/series/my-show',
    actor: ['https://example.com/actor/john'],
    duration: 2400,
    releaseDate: '2024-01-15'
);
```

#### Music Content

For music-related content:

```php
// Music song
SeoKit::opengraph()->musicSong(
    duration: 240,
    album: ['https://example.com/album/my-album'],
    albumDisc: 1,
    albumTrack: 5,
    musician: ['https://example.com/artist/john-doe']
);

// Music album
SeoKit::opengraph()->musicAlbum(
    song: ['https://example.com/song/track-1', 'https://example.com/song/track-2'],
    musician: ['https://example.com/artist/john-doe'],
    releaseDate: '2024-01-01'
);
```

#### Profile

For profile pages:

```php
SeoKit::opengraph()->profile(
    firstName: 'John',
    lastName: 'Doe',
    username: 'johndoe',
    gender: 'male'
);
```

#### Book

For book-related content:

```php
SeoKit::opengraph()->book(
    author: ['https://example.com/author/john-doe'],
    isbn: '978-3-16-148410-0',
    releaseDate: '2024-01-01',
    tags: ['fiction', 'thriller']
);
```

### Twitter Cards

#### Summary Card

```php
SeoKit::twitter()
    ->card('summary')
    ->site('@mysite')
    ->creator('@johndoe')
    ->title('Page Title')
    ->description('Page description')
    ->image('https://example.com/image.jpg', 'Image alt text');
```

#### Large Image Summary Card

```php
SeoKit::twitter()
    ->card('summary_large_image')
    ->title('Page Title')
    ->description('Page description')
    ->image('https://example.com/large-image.jpg');
```

#### Player Card

For video or audio content:

```php
SeoKit::twitter()
    ->card('player')
    ->player('https://example.com/player.html', 640, 480);
```

### JSON-LD Structured Data

#### Website Schema

```php
SeoKit::jsonld()->website([
    'url' => 'https://example.com',
    'name' => 'My Website',
    'description' => 'A great website',
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => 'https://example.com/search?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
]);
```

#### Organization Schema

```php
SeoKit::jsonld()->organization([
    'name' => 'My Company',
    'url' => 'https://example.com',
    'logo' => 'https://example.com/logo.png',
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+1-555-555-5555',
        'contactType' => 'customer service',
    ],
    'sameAs' => [
        'https://facebook.com/mycompany',
        'https://twitter.com/mycompany',
        'https://linkedin.com/company/mycompany',
    ],
]);
```

#### Article/Blog Post Schema

```php
SeoKit::jsonld()->article([
    'headline' => 'My Blog Post Title',
    'description' => 'A compelling description',
    'image' => 'https://example.com/image.jpg',
    'author' => [
        '@type' => 'Person',
        'name' => 'John Doe',
        'url' => 'https://example.com/author/john-doe',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'My Website',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => 'https://example.com/logo.png',
        ],
    ],
    'datePublished' => '2024-01-15T08:00:00+00:00',
    'dateModified' => '2024-01-16T10:30:00+00:00',
]);
```

#### Product Schema

```php
SeoKit::jsonld()->product([
    'name' => 'Product Name',
    'image' => 'https://example.com/product.jpg',
    'description' => 'Product description',
    'sku' => 'ABC123',
    'brand' => [
        '@type' => 'Brand',
        'name' => 'Brand Name',
    ],
    'offers' => [
        '@type' => 'Offer',
        'url' => 'https://example.com/product',
        'priceCurrency' => 'USD',
        'price' => '29.99',
        'priceValidUntil' => '2024-12-31',
        'availability' => 'https://schema.org/InStock',
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.5',
        'reviewCount' => '125',
    ],
]);
```

#### Local Business Schema

```php
SeoKit::jsonld()->localBusiness([
    'name' => 'My Business',
    'image' => 'https://example.com/business.jpg',
    'description' => 'Business description',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '123 Main St',
        'addressLocality' => 'New York',
        'addressRegion' => 'NY',
        'postalCode' => '10001',
        'addressCountry' => 'US',
    ],
    'telephone' => '+1-555-555-5555',
    'openingHours' => 'Mo-Fr 09:00-17:00',
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => '40.7128',
        'longitude' => '-74.0060',
    ],
    'priceRange' => '$$',
]);
```

#### Custom Schema

Add any custom schema:

```php
SeoKit::jsonld()->add([
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => 'My Event',
    'startDate' => '2024-06-15T19:00:00-05:00',
    'endDate' => '2024-06-15T23:00:00-05:00',
    'location' => [
        '@type' => 'Place',
        'name' => 'Event Venue',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '123 Event St',
            'addressLocality' => 'New York',
            'addressRegion' => 'NY',
            'postalCode' => '10001',
        ],
    ],
]);
```

## Database-backed SEO

SeoKit provides two approaches for storing SEO data in your database.

### Using the HasSeo Trait

This approach stores SEO data in a separate polymorphic table, allowing you to manage SEO independently from your model's main attributes.

#### Step 1: Add the Trait

Add the `HasSeo` trait to your model:

```php
use Illuminate\Database\Eloquent\Model;
use Larament\SeoKit\Concerns\HasSeo;

class Post extends Model
{
    use HasSeo;
}
```

#### Step 2: Create SEO Data

```php
$post = Post::find(1);

$post->seo()->create([
    'title' => 'Custom SEO Title',
    'description' => 'Custom SEO description for search engines',
    'canonical' => 'https://example.com/posts/custom-url',
    'robots' => 'index, follow',
    'og_title' => 'Custom OG Title',
    'og_description' => 'Custom OG description for social sharing',
    'og_image' => 'https://example.com/images/og-image.jpg',
    'twitter_image' => 'https://example.com/images/twitter-image.jpg',
    'structured_data' => [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => 'My Article',
    ],
    'is_cornerstone' => true, // Mark as cornerstone content
]);
```

#### Step 3: Apply SEO Tags

In your controller, call `prepareSeoTags()`:

```php
public function show(Post $post)
{
    $post->prepareSeoTags();

    return view('posts.show', compact('post'));
}
```

The method will automatically retrieve cached SEO data and apply it to the page.

#### Updating SEO Data

```php
$post->seo()->update([
    'title' => 'Updated SEO Title',
    'description' => 'Updated description',
]);
```

#### Checking Cornerstone Content

```php
if ($post->isCornerstone()) {
    // This is cornerstone content
}
```

#### Caching Behavior

The `HasSeo` trait automatically caches SEO data using Laravel's cache system. The cache is automatically invalidated when:

-   The SEO data is updated
-   The model is deleted

### Using the HasSeoData Trait

This approach is ideal when you want to derive SEO data from your model's existing attributes without storing separate SEO records.

#### Step 1: Add the Trait and Implement Method

```php
use Illuminate\Database\Eloquent\Model;
use Larament\SeoKit\Concerns\HasSeoData;
use Larament\SeoKit\Data\SeoData;

class Post extends Model
{
    use HasSeoData;

    private function toSeoData(): SeoData
    {
        return new SeoData(
            title: $this->title,
            description: $this->excerpt,
            canonical: route('posts.show', $this),
            robots: $this->is_published ? 'index, follow' : 'noindex, nofollow',
            og_image: $this->featured_image,
            structured_data: [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $this->title,
                'datePublished' => $this->published_at?->toIso8601String(),
                'dateModified' => $this->updated_at?->toIso8601String(),
            ],
        );
    }
}
```

#### Step 2: Apply SEO Tags

```php
public function show(Post $post)
{
    $post->prepareSeoTags();

    return view('posts.show', compact('post'));
}
```

## Complete Examples

### Blog Post Example

Controller:

```php
namespace App\Http\Controllers;

use App\Models\Post;
use Larament\SeoKit\Facades\SeoKit;

class PostController extends Controller
{
    public function show(Post $post)
    {
        // Option 1: Using database SEO (if using HasSeo or HasSeoData trait)
        $post->prepareSeoTags();

        // Option 2: Manual SEO setup
        SeoKit::title($post->title)
            ->description($post->excerpt)
            ->image($post->featured_image)
            ->canonical(route('posts.show', $post));

        SeoKit::opengraph()->article(
            publishedTime: $post->published_at?->toIso8601String(),
            modifiedTime: $post->updated_at?->toIso8601String(),
            authors: [$post->author->profile_url],
            section: $post->category->name,
            tags: $post->tags->pluck('name')->toArray()
        );

        SeoKit::jsonld()->article([
            'headline' => $post->title,
            'description' => $post->excerpt,
            'image' => $post->featured_image,
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name,
            ],
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
        ]);

        return view('posts.show', compact('post'));
    }
}
```

### E-commerce Product Example

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Larament\SeoKit\Facades\SeoKit;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        SeoKit::title($product->name)
            ->description($product->short_description)
            ->image($product->primary_image)
            ->canonical(route('products.show', $product));

        SeoKit::opengraph()
            ->type('product')
            ->add('product:price:amount', $product->price)
            ->add('product:price:currency', 'USD');

        SeoKit::jsonld()->product([
            'name' => $product->name,
            'image' => $product->images->pluck('url')->toArray(),
            'description' => $product->description,
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product),
                'priceCurrency' => 'USD',
                'price' => $product->price,
                'availability' => $product->in_stock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->average_rating,
                'reviewCount' => $product->reviews_count,
            ],
        ]);

        return view('products.show', compact('product'));
    }
}
```

### Homepage with Organization Schema

```php
namespace App\Http\Controllers;

use Larament\SeoKit\Facades\SeoKit;

class HomeController extends Controller
{
    public function index()
    {
        SeoKit::title('Welcome to Our Website')
            ->description('Discover amazing products and services');

        SeoKit::jsonld()->organization([
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-555-555-5555',
                'contactType' => 'customer service',
                'email' => 'support@example.com',
            ],
            'sameAs' => [
                'https://facebook.com/yourpage',
                'https://twitter.com/yourhandle',
                'https://linkedin.com/company/yourcompany',
                'https://instagram.com/yourprofile',
            ],
        ]);

        SeoKit::jsonld()->website([
            'url' => config('app.url'),
            'name' => config('app.name'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ]);

        return view('home');
    }
}
```

## Testing

Run the tests with:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

Run static analysis:

```bash
composer analyse
```

Format code:

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Raziul Islam](https://github.com/iRaziul)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
