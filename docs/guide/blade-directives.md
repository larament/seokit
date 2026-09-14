# Blade Directives

SeoKit provides an ergonomic Blade directive that makes integrating SEO tags into your Laravel layouts straightforward and clean.

## The `@seoKit` Directive

Place `@seoKit` inside the `<head>` section of your application's master layout (typically `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @seoKit

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    @yield('content')
</body>
</html>
```

### What `@seoKit` Renders

When invoked, `@seoKit` evaluates each subsystem enabled in `config/seokit.php` and outputs:
1. `<title>`, `<meta name="description">`, `<meta name="robots">`, `<meta name="keywords">`, `<link rel="canonical">`, and `<link rel="alternate">`
2. Open Graph `<meta property="og:...">` tags (if `opengraph.enabled` is `true`)
3. Twitter Card `<meta name="twitter:...">` tags (if `twitter.enabled` is `true`)
4. Schema.org `<script type="application/ld+json">` blocks (if `json_ld.enabled` is `true`)

---

## Minified Output

To remove line breaks and minify tag output in production environments:

```blade
@seoKit(true)
```

You can also pass environment checks dynamically:

```blade
@seoKit(app()->isProduction())
```

---

## Selective & Fine-Grained Rendering

If you ever need to render specific subsections in separate locations or within dedicated view components, you can call the underlying managers directly via Blade:

```blade
{{-- Render only Meta Tags --}}
{!! SeoKit::meta()->toHtml() !!}

{{-- Render only Open Graph tags --}}
{!! SeoKit::opengraph()->toHtml() !!}

{{-- Render only Twitter Card tags --}}
{!! SeoKit::twitter()->toHtml() !!}

{{-- Render only JSON-LD schema scripts --}}
{!! SeoKit::jsonld()->toHtml() !!}
```
