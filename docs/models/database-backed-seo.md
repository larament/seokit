# Database-Backed SEO

SeoKit allows you to attach dedicated SEO records to any Eloquent model using a polymorphic `MorphOne` relationship. This keeps your domain tables clean (no need to add 15+ SEO columns to your `posts`, `products`, or `users` tables).

## 1. Adding the `HasSeo` Trait

Add `Larament\SeoKit\Concerns\HasSeo` to your Eloquent model:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Larament\SeoKit\Concerns\HasSeo;

class Article extends Model
{
    use HasSeo;

    // Optional: provide sensible fallback values if no DB record exists
    protected function fallbackSeoData(): \Larament\SeoKit\Data\SeoData
    {
        return new \Larament\SeoKit\Data\SeoData(
            title: $this->title,
            description: $this->summary,
            og_image: $this->featured_image,
        );
    }
}
```

---

## 2. Managing the Related SEO Record

The `HasSeo` trait exposes the `seo()` polymorphic relation:

```php
// Create or update SEO record for the article
$article->seo()->updateOrCreate([], [
    'title' => 'Custom Meta Title for Search Engines',
    'description' => 'Targeted snippet designed for high click-through rate.',
    'keywords' => 'laravel, php, seo, packages',
    'canonical' => 'https://example.com/canonical-url',
    'robots' => 'index, follow',
    'og_title' => 'Social-Specific Headline',
    'og_description' => 'Catchy teaser description for social feeds.',
    'og_image' => 'articles/covers/hero.png',
    'og_image_disk' => 's3',
    'twitter_image' => 'articles/covers/twitter-card.png',
    'twitter_image_disk' => 's3',
    'is_cornerstone' => true,
    'structured_data' => [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $article->title,
    ],
]);
```

---

## 3. Applying SEO Data in Controllers

In your controller, simply call `prepareSeoTags()` on the model instance:

```php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function show(Article $article): View
    {
        // Hydrates SeoKit with the cached DB record (and fallbacks)
        $article->prepareSeoTags();

        return view('articles.show', compact('article'));
    }
}
```

That's it! The `@seoKit` directive in your Blade layout will now render the complete metadata for this article.

---

## Automatic Caching & Cache Invalidation

To guarantee maximum database performance:
1. When `seoData()` is fetched, SeoKit caches the data permanently via `Cache::rememberForever(...)`.
2. Whenever the related `Seo` model is saved, updated, or deleted, the cache entry is invalidated automatically.
3. Whenever the parent model is deleted or force-deleted, the related `Seo` record and cache are cleaned up.
