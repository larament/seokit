# JSON-LD Structured Data

Schema.org structured data in JSON-LD format enables search engines (like Google) to understand your content deeply, unlocking rich results such as Article cards, Star ratings, Breadcrumb trails, and Product pricing snippets.

SeoKit provides built-in schema generators for common entity types and full support for custom Schema.org objects.

## Using Built-in Schemas

Access the JSON-LD manager via `SeoKit::jsonld()`:

### WebSite Schema

```php
use Larament\SeoKit\Facades\SeoKit;

SeoKit::jsonld()->website([
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => 'https://example.com/search?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
]);
```

### Organization Schema

```php
SeoKit::jsonld()->organization([
    'logo' => 'https://example.com/logo.png',
    'sameAs' => [
        'https://twitter.com/myorg',
        'https://github.com/myorg',
        'https://linkedin.com/company/myorg',
    ],
]);
```

### Article & BlogPosting Schema

```php
SeoKit::jsonld()->article([
    'headline' => $post->title,
    'description' => $post->excerpt,
    'image' => $post->featured_image_url,
    'author' => [
        '@type' => 'Person',
        'name' => $post->author->name,
        'url' => route('authors.show', $post->author),
    ],
    'datePublished' => $post->published_at->toISOString(),
    'dateModified' => $post->updated_at->toISOString(),
]);
```

For blog posts specifically, you can also use `SeoKit::jsonld()->blogPosting([...])`.

### Product Schema

Ideal for e-commerce stores:

```php
SeoKit::jsonld()->product([
    'name' => 'Ergonomic Mechanical Keyboard',
    'image' => 'https://example.com/images/keyboard.jpg',
    'description' => 'Compact mechanical keyboard with hot-swappable switches.',
    'sku' => 'KB-900',
    'offers' => [
        '@type' => 'Offer',
        'price' => '149.00',
        'priceCurrency' => 'USD',
        'availability' => 'https://schema.org/InStock',
        'url' => route('products.show', 'keyboard'),
    ],
]);
```

### LocalBusiness Schema

```php
SeoKit::jsonld()->localBusiness([
    'name' => 'Acme Coffee Roasters',
    'image' => 'https://example.com/store.jpg',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '123 Main St',
        'addressLocality' => 'Austin',
        'addressRegion' => 'TX',
        'postalCode' => '78701',
    ],
    'telephone' => '+1-555-0199',
    'openingHours' => 'Mo-Fr 07:00-19:00',
]);
```

---

## Custom Schemas

You can pass any arbitrary Schema.org array to `add()`:

### BreadcrumbList Schema

```php
SeoKit::jsonld()->add([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Blog',
            'item' => url('/blog'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $post->title,
            'item' => url()->current(),
        ],
    ],
]);
```

### FAQPage Schema

```php
SeoKit::jsonld()->add([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'Is SeoKit compatible with Laravel Octane?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes! SeoKit uses request-scoped bindings to prevent state pollution in Octane.',
            ],
        ],
    ],
]);
```

---

## Output Script Formatting

When rendered, SeoKit converts schemas into secure, escaped `<script type="application/ld+json">` tags:

```html
<script type="application/ld+json">{"@context":"https://schema.org","@type":"Article","headline":"Laravel Performance Tips"}</script>
```

You can clear all registered schemas using `SeoKit::jsonld()->clear()`.
