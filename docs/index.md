---
layout: home

hero:
  name: "SeoKit"
  text: "Technical SEO Made Easy in Laravel"
  tagline: "A unified, elegant toolkit for Meta Tags, Open Graph, Twitter Cards, Schema JSON-LD, and Database-backed SEO."
  actions:
    - theme: brand
      text: Get Started
      link: /getting-started/installation
    - theme: alt
      text: Quick Start Guide
      link: /getting-started/quick-start
    - theme: alt
      text: GitHub
      link: https://github.com/larament/seokit

features:
  - icon: 🏷️
    title: Complete Meta Tag Control
    details: Effortlessly control titles, prefixes, suffixes, automatic URL inference, descriptions, keywords, robots directives, and canonical URLs.
  - icon: 🌐
    title: Social Sharing (OG & Twitter)
    details: Rich Open Graph and Twitter Card tags with complete support for images, video, audio, article metadata, and multiple handles.
  - icon: 🧩
    title: JSON-LD Structured Data
    details: Fluent Schema.org builders for Organizations, WebSites, Articles, BreadcrumbLists, Products, and custom schema graphs.
  - icon: 🗄️
    title: Polymorphic Eloquent SEO
    details: Attach SEO records to any Eloquent model with HasSeo trait, complete with automated cache invalidation on save and delete.
  - icon: ⚡
    title: Computed Model SEO
    details: Implement HasSeoData and toSeoData() to dynamically map model attributes directly to complete SEO datasets.
  - icon: 🚀
    title: Octane & Modern Laravel Ready
    details: Request-scoped instances ensure zero memory bleed in long-running environments like Laravel Octane, FrankenPHP, and RoadRunner.
---

<div class="vp-doc" style="max-width: 900px; margin: 40px auto 0;">

## Why SeoKit?

Managing SEO in modern Laravel applications often means stitching together multiple single-purpose packages, maintaining repetitive Blade templates, or scattering raw `<meta>` tags across controllers, models, and views.

**SeoKit solves this with a single, expressive API:**

::: code-group

```php [Controller]
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

```blade [Layout (resources/views/layouts/app.blade.php)]
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Generates Meta Tags, OpenGraph, Twitter Cards, and JSON-LD --}}
    @seoKit

</head>
<body>
    @yield('content')
</body>
</html>
```

```php [Eloquent Model]
use Illuminate\Database\Eloquent\Model;
use Larament\SeoKit\Concerns\HasSeo;

class Post extends Model
{
    use HasSeo; // [!code ++]
}

// Access or attach SEO data directly
$post->seo()->updateOrCreate([], [
    'title' => 'Custom Search Title',
    'description' => 'Fine-tuned search snippet',
    'og_image' => 'marketing/og-banner.jpg',
]);
```

:::

</div>
