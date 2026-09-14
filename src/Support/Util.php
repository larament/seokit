<?php

declare(strict_types=1);

namespace Larament\SeoKit\Support;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Middleware;
use Larament\SeoKit\Exceptions\InvalidImageUrlException;

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
    public static function getTitleFromUrl(): string
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
        return e(strip_tags(
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

    /**
     * Resolve the image path or URL into a fully-qualified absolute URL.
     */
    public static function resolveImageUrl(?string $path, ?string $disk = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::isUrl($path)) {
            return $path;
        }

        if (filled($disk)) {
            $url = Storage::disk($disk)->url($path);

            return Str::isUrl($url) ? $url : url($url);
        }

        if (app()->isProduction()) {
            Log::error(sprintf('The image path [%s] cannot be resolved because no storage disk was specified.', $path));

            return null;
        }

        throw InvalidImageUrlException::missingDisk($path);
    }
}
