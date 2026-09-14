# Meta Tags & Titles

SeoKit gives you full control over HTML `<title>`, `<meta>`, and `<link>` tags through a fluent and expressive API.

## Title Management

### Setting a Page Title

Set the title using either the `SeoKit` facade shortcut or the `meta()` sub-manager:

```php
use Larament\SeoKit\Facades\SeoKit;

// Sets title across Meta, Open Graph, and Twitter
SeoKit::title('My Page Title');

// Or specifically on MetaTags:
SeoKit::meta()->title('My Page Title');
```

When rendered, the title will automatically respect `title_separator`, `before_title`, and `after_title` configured in `config/seokit.php`.

### Inferred Titles from URL

If you do not call `title()`, SeoKit automatically infers a title from the URL's last path segment if `auto_title_from_url` is enabled:

```php
// Visiting URL: /blog/advanced-eloquent-patterns
// Renders:
<title>Advanced Eloquent Patterns - My App</title>
```

You can customize this inference logic in `config/seokit.php`:

```php
'title_inference_callback' => fn (string $slug) => ucfirst(str_replace('-', ' ', $slug)),
```

---

## Meta Description & Keywords

```php
use Larament\SeoKit\Facades\SeoKit;

// Description
SeoKit::description('Explore best practices for building scalable Laravel web applications.');

// Keywords
SeoKit::meta()->keywords(['laravel', 'php', 'seo', 'web-development']);
```

---

## Canonical URLs

By default, SeoKit generates canonical tags using `URL::current()`. You can customize or override the canonical URL for any route:

```php
use Larament\SeoKit\Facades\SeoKit;

// Specific canonical URL
SeoKit::canonical('https://example.com/canonical-post');

// Or via the meta manager:
SeoKit::meta()->canonical('https://example.com/canonical-post');
```

---

## Robots Meta Directives

Control how search engine crawlers index and follow links on the page. SeoKit supports plain strings, arrays, or the typed `MetaRobots` enum:

```php
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\Enums\MetaRobots;

// Using typed Enums:
SeoKit::meta()->robots([
    MetaRobots::Noindex,
    MetaRobots::NoFollow,
]);

// Or as a string:
SeoKit::meta()->robots('noindex, nofollow');
```

Available enum cases in `Larament\SeoKit\Enums\MetaRobots`:
- `MetaRobots::Index` (`'index'`)
- `MetaRobots::Noindex` (`'noindex'`)
- `MetaRobots::Follow` (`'follow'`)
- `MetaRobots::NoFollow` (`'nofollow'`)
- `MetaRobots::NoArchive` (`'noarchive'`)
- `MetaRobots::NoImageIndex` (`'noimageindex'`)
- `MetaRobots::NoSnippet` (`'nosnippet'`)

---

## Alternate Languages (hreflang)

Add multilingual alternate links for internationalized web applications:

```php
SeoKit::meta()
    ->addLanguage('en', 'https://example.com/en/article')
    ->addLanguage('es', 'https://example.com/es/articulo')
    ->addLanguage('fr', 'https://example.com/fr/article')
    ->addLanguage('x-default', 'https://example.com/article');
```

This compiles to:
```html
<link rel="alternate" hreflang="en" href="https://example.com/en/article">
<link rel="alternate" hreflang="es" href="https://example.com/es/articulo">
<link rel="alternate" hreflang="fr" href="https://example.com/fr/article">
<link rel="alternate" hreflang="x-default" href="https://example.com/article">
```

To remove a language:
```php
SeoKit::meta()->removeLanguage('fr');
```

---

## Custom Meta and Link Tags

Add arbitrary `<meta>` and `<link>` tags to accommodate verification tags, theme colors, or custom attributes:

```php
// Custom meta tags
SeoKit::meta()
    ->addMeta('theme-color', '#10B981')
    ->addMeta('google-site-verification', 'your-token-here');

// Custom links
SeoKit::meta()
    ->addLink('author', 'https://raziul.dev')
    ->addLink('next', 'https://example.com/posts?page=2');
```
