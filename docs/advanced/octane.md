# Laravel Octane & High-Concurrency Safety

Modern Laravel applications increasingly run on long-lived application runtimes such as:
- **Laravel Octane** (powered by Swoole or RoadRunner)
- **FrankenPHP** (Worker Mode)
- **Swoole / OpenSwoole**

In these environments, the PHP application boots once into memory and handles thousands of subsequent HTTP requests in a persistent loop.

## The Challenge with Singletons in Octane

In traditional PHP-FPM, PHP processes shut down completely after every request, resetting all globals and singletons.

In worker-mode runtimes (like Octane), standard container singletons (`$app->singleton()`) persist across requests. If a package stores request-specific metadata (such as page titles, descriptions, or user-specific OG tags) on a persistent singleton, **that metadata will leak across requests to other users**.

---

## How SeoKit Guarantees Request Isolation

SeoKit is engineered specifically for modern Laravel environments.

In `SeoKitServiceProvider`, SeoKit registers `SeoKitManager` using Laravel's request-scoped binding:

```php
$this->app->scoped(SeoKitManager::class, fn (): SeoKitManager => new SeoKitManager(
    new MetaTags,
    new OpenGraph,
    new TwitterCards,
    new JsonLD
));
```

### What `app->scoped()` Does:
1. **Fresh Per Request**: For every incoming HTTP request, Laravel resolves a new, isolated instance of `SeoKitManager`.
2. **Zero Cross-Request Contamination**: Metadata, titles, images, and JSON-LD schemas set during one request can never leak into another.
3. **Automatic Cleanup**: At the end of the HTTP request lifecycle, the container flushes the scoped instance, freeing memory and eliminating memory leaks.
4. **Zero Configuration Needed**: You do not need to register any listeners in `config/octane.php` `flush` arrays. SeoKit works out-of-the-box.
