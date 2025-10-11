<?php

declare(strict_types=1);

// config for Larament/SeoKit
return [
    /*
     * The table name to be used by the Seo model and migration.
     * You can change this to any table name you prefer.
     */
    'table_name' => 'seokit',

    /*
    |--------------------------------------------------------------------------
    | Auto Title From URL
    |--------------------------------------------------------------------------
    |
    | Automatically generate a human-readable title based on the last segment
    | of the current URL when no explicit title is provided. This is useful for
    | dynamic pages or routes without associated models.
    |
    | Example:
    |   URL: '/blog/getting-started' → Title: 'Getting Started'
    |
    | Note:
    |   - Applies only when `title` is not set explicitly.
    |   - Uses Str::of($slug)->headline() by default.
    |
    */
    'auto_title_from_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Title Inference Callback (Optional)
    |--------------------------------------------------------------------------
    |
    | You can optionally provide a custom callable (closure, invokable class, etc.)
    | to control how inferred titles are generated from the URL segment.
    |
    | Example:
    |   fn (string $slug): string => ucfirst(str_replace('-', ' ', $slug))
    |
    | Leave null to use the default behavior.
    |
    */
    'title_inference_callback' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Meta Tags
    |--------------------------------------------------------------------------
    |
    | Default meta tags that will be present across all pages unless overridden.
    |
    */
    'defaults' => [
        // The default title to use if no explicit title is provided and `auto_title_from_url` is false
        'title' => 'Be TALL or not at all',

        // Text to prepend before the title (string or callable)
        'before_title' => null,

        // Text to append after the title (string or callable)
        'after_title' => null,

        // Separator to use between before_title, title, and after_title
        'title_separator' => ' - ',

        // Default meta description for pages
        'description' => null,

        // Default canonical URL for pages
        // Use null for `URL::current()`, full for `URL::full()`, or false to remove
        'canonical' => null,

        // Default robots meta tag directives (e.g. index, follow, etc.)
        'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph
    |--------------------------------------------------------------------------
    |
    | Open Graph meta tags for better social sharing.
    |
    */
    'opengraph' => [
        'enabled' => true,
        'defaults' => [
            'site_name' => config('app.name', 'Laravel'),
            'type' => 'website',
            'url' => null, // Use null for `URL::current()`, 'full' for `URL::full()`, or false to remove
            'locale' => 'en_US',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Card
    |--------------------------------------------------------------------------
    |
    | Twitter Card meta tags for better Twitter sharing.
    |
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
    | JSON-LD
    |--------------------------------------------------------------------------
    |
    | JSON-LD meta tags for better structured data.
    |
    */
    'json_ld' => [
        'enabled' => true,
        'defaults' => [],
    ],
];
