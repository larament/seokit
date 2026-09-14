# Configuration

After publishing the configuration file via `php artisan seokit:install` or `php artisan vendor:publish --tag="seokit-config"`, you will find your settings at `config/seokit.php`.

Here is the complete reference for all available options and how they behave.

## Full Configuration Reference

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Database Table Name
    |--------------------------------------------------------------------------
    | The table name used by the Seo model and migration for polymorphic records.
    */
    'table_name' => 'seokit',

    /*
    |--------------------------------------------------------------------------
    | Auto Title From URL
    |--------------------------------------------------------------------------
    | Automatically generate a human-readable title based on the last segment
    | of the current URL when no explicit title is provided.
    | Example: URL '/blog/getting-started' → Title 'Getting Started'
    */
    'auto_title_from_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Title Inference Callback
    |--------------------------------------------------------------------------
    | A custom callable (closure or invokable class) to control how inferred
    | titles are generated from URL segments.
    */
    'title_inference_callback' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Meta Tags
    |--------------------------------------------------------------------------
    | Meta tag defaults present across all pages unless overridden.
    */
    'defaults' => [
        'title' => 'Be TALL or not at all',
        'before_title' => null,
        'after_title' => null,
        'title_separator' => ' - ',
        'description' => null,
        'canonical' => null, // null for URL::current(), 'full' for URL::full(), or false to disable
        'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph
    |--------------------------------------------------------------------------
    | Default Open Graph parameters for social platforms like Facebook & LinkedIn.
    */
    'opengraph' => [
        'enabled' => true,
        'defaults' => [
            'site_name' => null, // Defaults to config('app.name') at runtime
            'type' => 'website',
            'url' => null,       // null for URL::current(), 'full' for URL::full(), or false
            'locale' => 'en_US',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Cards
    |--------------------------------------------------------------------------
    | Default Twitter Card parameters.
    */
    'twitter' => [
        'enabled' => true,
        'defaults' => [
            'card' => 'summary_large_image',
            'site' => '@raziuldev',
            'creator' => '@raziuldev',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-LD Structured Data
    |--------------------------------------------------------------------------
    | Default structured data schemas.
    */
    'json_ld' => [
        'enabled' => true,
        'defaults' => [],
    ],
];
```

---

## Detailed Option Explanations

### `table_name`
- **Default**: `'seokit'`
- **Type**: `string`

Specifies the name of the database table used by SeoKit's polymorphic `Seo` model. If your application already has a table with this name, change it here before running `php artisan migrate`.

---

### `auto_title_from_url`
- **Default**: `true`
- **Type**: `bool`

When enabled, if `SeoKit::title()` is not called explicitly on a request, SeoKit extracts the last path segment from the current request URL and transforms it into a headline (via `Illuminate\Support\Str::headline()`).

For instance:
- Request `/products/wireless-keyboard` → Title becomes `Wireless Keyboard`.

---

### `title_inference_callback`
- **Default**: `null`
- **Type**: `callable|null`

Customize the transformation of the URL slug into a title. You can supply a Closure or class:

```php
'title_inference_callback' => function (string $slug): string {
    return ucwords(str_replace(['-', '_'], ' ', $slug));
},
```

---

### Title Formatting (`before_title`, `after_title`, `title_separator`)

You can set global prefixes or suffixes to wrap all page titles:

```php
'defaults' => [
    'title_separator' => ' | ',
    'after_title' => config('app.name'),
],
```

If you call `SeoKit::title('Documentation')`, the resulting title rendered in HTML will be:
```html
<title>Documentation | My Application</title>
```

You can also pass a closure to `before_title` or `after_title` to resolve dynamic values per request.

---

### `canonical`
- **Default**: `null`
- **Options**:
  - `null`: Defaults to `URL::current()` (omits query strings).
  - `'full'`: Uses `URL::full()` (includes query strings).
  - `false`: Disables the canonical tag output entirely.
  - `string`: Any explicit URL specified per route.

---

### `robots`
- **Default**: `'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'`
- **Type**: `string`

The default Google and search bot directives. You can override this per page in your controller via `SeoKit::meta()->robots('noindex, nofollow')`.
