<?php

declare(strict_types=1);

namespace Larament\SeoKit\Support;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

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
}
