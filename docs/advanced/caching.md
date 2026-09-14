# Caching & Invalidation

To maintain lightning-fast response times and minimize database I/O, SeoKit implements automatic, zero-configuration caching for model SEO data.

## Cache Key Structure

For models using the `HasSeo` trait, SeoKit generates a deterministic cache key based on the model's morph class and primary key:

```
seokit.{morphed_model_class}.{primary_key}
```

For example, an `App\Models\Article` with ID `42` produces:

```
seokit.App.Models.Article.42
```

---

## Caching Strategy (`rememberForever`)

When `prepareSeoTags()` or `$model->seoData()` is executed:
1. SeoKit checks the application's default cache store (Redis, Memcached, DynamoDB, etc.).
2. If absent, the query runs once to load the related `Seo` record and stores the resulting array via `Cache::rememberForever()`.
3. Subsequent requests load the SEO payload directly from the cache with zero database queries.

---

## Automatic Invalidation Lifecycle

SeoKit keeps cache data fresh through Eloquent model lifecycle hooks:

### 1. When the `Seo` Record Changes
Whenever a `Seo` record is created, updated, or deleted, its `booted()` model observer automatically purges the parent model's cache key:
```php
Cache::forget(Util::modelCacheKey($seo->model));
```

### 2. When the Parent Model is Deleted
When a model using `HasSeo` is deleted:
- The associated `Seo` polymorphic record is deleted automatically (on force-delete).
- The cached key is immediately cleared.

---

## Manual Invalidation

If you ever update database records outside Eloquent (e.g. via raw database queries or mass updates), you can manually clear the cache key using `Larament\SeoKit\Support\Util`:

```php
use Illuminate\Support\Facades\Cache;
use Larament\SeoKit\Support\Util;

Cache::forget(Util::modelCacheKey($article));
```
