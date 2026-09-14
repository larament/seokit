# Quick Start

Get up and running with SeoKit in under 5 minutes.

## 1. Include the Blade Directive in your Layout

Open your main HTML layout file (e.g. `resources/views/layouts/app.blade.php`) and add the `@seoKit` directive inside the `<head>` tag:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Render all SEO meta tags, Open Graph, Twitter, and JSON-LD --}}
    @seoKit

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    @yield('content')
</body>
</html>
```

The `@seoKit` directive automatically compiles and renders:
- Standard `<title>` and `<meta name="...">` tags
- Open Graph `<meta property="og:...">` tags
- Twitter Card `<meta name="twitter:...">` tags
- Schema.org `<script type="application/ld+json">` blocks

---

## 2. Set Metadata in Your Controllers

Use the `Larament\SeoKit\Facades\SeoKit` facade to define page metadata fluently:

```php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Larament\SeoKit\Facades\SeoKit;

class PostController extends Controller
{
    public function show(Post $post): View
    {
        // Fluent top-level shortcuts:
        SeoKit::title($post->title)
            ->description($post->excerpt)
            ->image($post->featured_image)
            ->canonical(route('posts.show', $post));

        return view('posts.show', compact('post'));
    }
}
```

::: tip Fluent Convenience Shortcuts
The `SeoKit::title()`, `SeoKit::description()`, `SeoKit::image()`, and `SeoKit::canonical()` methods synchronize across all layers (Meta Tags, Open Graph, and Twitter Cards) automatically. You don't have to define them repeatedly.
:::

---

## 3. Working with Eloquent Models

If your model uses the `HasSeo` trait, you can hydrate SeoKit directly in a single call:

```php
use App\Models\Post;
use Larament\SeoKit\Facades\SeoKit;

public function show(Post $post)
{
    // If Post uses HasSeo or HasSeoData:
    if ($post->seoData) {
        SeoKit::fromSeoData($post->seoData);
    }

    return view('posts.show', compact('post'));
}
```

---

## 4. Automatic Title Fallbacks

If a page does not set an explicit title, SeoKit automatically converts the trailing URL segment into a clean headline by default.

For example:
- Visiting `/blog/getting-started` automatically renders:
  ```html
  <title>Getting Started - Laravel</title>
  ```

You can customize or disable this in `config/seokit.php`.

---

## What the Generated HTML Looks Like

When your page renders, SeoKit outputs clean, structured markup:

```html
<title>10 Tips for Laravel Developers - My App</title>
<meta name="description" content="Discover modern Laravel practices to elevate your application architecture.">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<link rel="canonical" href="https://example.com/posts/10-tips">

<!-- Open Graph -->
<meta property="og:title" content="10 Tips for Laravel Developers">
<meta property="og:description" content="Discover modern Laravel practices to elevate your application architecture.">
<meta property="og:url" content="https://example.com/posts/10-tips">
<meta property="og:type" content="website">
<meta property="og:site_name" content="My App">
<meta property="og:locale" content="en_US">
<meta property="og:image" content="https://example.com/storage/featured.jpg">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@myapp">
<meta name="twitter:creator" content="@myapp">
<meta name="twitter:title" content="10 Tips for Laravel Developers">
<meta name="twitter:description" content="Discover modern Laravel practices to elevate your application architecture.">
<meta name="twitter:image" content="https://example.com/storage/featured.jpg">
```
