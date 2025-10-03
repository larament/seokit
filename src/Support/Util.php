<?php

declare(strict_types=1);

namespace Larament\SeoKit\Support;

use Closure;
use Illuminate\Support\Facades\Request;

final class Util
{
    /**
     * Format the title with before, after, and separator.
     */
    public static function formatTitle(string $title): string
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
        if (! config('seokit.auto_title_from_url')) {
            return null;
        }

        $slug = str(Request::path())->afterLast('/')->beforeLast('.');
        $callback = config('seokit.title_inference_callback');

        if ($callback instanceof Closure) {
            return $callback((string) $slug);
        }

        return $slug->headline()->toString();
    }

    /**
     * Clean the string by removing http-equiv, url, and html tags.
     */
    public static function cleanString(string $string): string
    {
        return strip_tags(str_replace(['http-equiv=', 'url='], '', $string));
    }
}
