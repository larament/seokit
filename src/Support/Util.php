<?php

declare(strict_types=1);

namespace Larament\SeoKit\Support;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

final class Util
{
    /**
     * Affix the title with before, after, and separator.
     */
    public static function affixTitle(string $title): string
    {
        $before = value(config('seokit.defaults.before_title'), $title);
        $after = value(config('seokit.defaults.after_title'), $title);

        if (! $before && ! $after) {
            return $title;
        }

        return implode(config('seokit.defaults.title_separator'), array_filter([$before, $title, $after]));
    }

    /**
     * Get the title from the URL.
     */
    public static function getTitleFromUrl(): ?string
    {
        $path = Request::path();

        if ($path === '/') {
            return config('app.name');
        }

        $slug = str($path)->afterLast('/')->beforeLast('.');
        $callback = config('seokit.title_inference_callback');

        if ($callback instanceof Closure) {
            return $callback((string) $slug);
        }

        return $slug->headline()->trim()->toString();
    }

    /**
     * Clean the string by removing http-equiv, url, and html tags.
     */
    public static function cleanString(string $string): string
    {
        return strip_tags(e(
            str_replace(['http-equiv=', 'url='], '', $string)
        ));
    }

    /**
     * Get the unique cache key for the model's SEO data.
     */
    public static function modelCacheKey(Model $model): string
    {
        return sprintf('seokit.%s.%s', str_replace('\\', '.', $model->getMorphClass()), $model->getKey());
    }

    /**
     * Check if the current route is an Inertia route.
     */
    public static function isInertiaRoute(): bool
    {
        if (! $currentRoute = Route::current()) {
            return false;
        }

        return collect(Route::gatherRouteMiddleware($currentRoute))->contains(
            // @phpstan-ignore-next-line
            fn (string|Closure $middleware): bool => ! $middleware instanceof Closure && is_subclass_of($middleware, Middleware::class)
        );
    }
}
