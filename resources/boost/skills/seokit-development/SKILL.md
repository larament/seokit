---
name: seokit-development
description: Integrate SeoKit package into Laravel applications for managing SEO metadata.
---

# SeoKit Development

## When to use this skill

Use this skill when working with SeoKit features to manage Meta tags, Open Graph, Twitter Cards, and JSON-LD structured data.

## Core Usage

### Setting SEO data globally

```php
use Larament\SeoKit\Facades\SeoKit;

SeoKit::title('Page Title');
SeoKit::description('Page description');
SeoKit::image('https://example.com/image.jpg');
SeoKit::canonical('https://example.com/page');
SeoKit::meta()->keywords(['laravel', 'seo']);
SeoKit::meta()->robots('index, follow');
```

### Rendering in Blade

```blade
<head>
    @seoKit
    <!-- or minified -->
    @seoKit(true)
</head>
```

## Model Integration

Database-backed SEO with optional fallback:

```php
use Larament\SeoKit\Concerns\HasSeo;
use Larament\SeoKit\Data\SeoData;

class Post extends Model
{
    use HasSeo;

    protected function fallbackSeoData(): SeoData
    {
        return new SeoData(
            title: $this->title,
            description: $this->excerpt,
        );
    }
}
```

Computed SEO directly from model:

```php
use Larament\SeoKit\Concerns\HasSeoData;
use Larament\SeoKit\Data\SeoData;

class Article extends Model
{
    use HasSeoData;

    public function toSeoData(): SeoData
    {
        return new SeoData(
            title: $this->title,
            description: $this->summary,
        );
    }
}
```

Then in your controller, call `prepareSeoTags()`:

```php
$post->prepareSeoTags();
```

## Available SeoData Fields

- `title`, `description`, `keywords`, `canonical`, `robots`
- `og_title`, `og_description`, `og_image`
- `twitter_image`
- `structured_data` (array for JSON-LD)

## Configuration

Publish and edit `config/seokit.php` to customize:

- Default title/description
- Open Graph, Twitter, JSON-LD settings
- Auto-title from URL
