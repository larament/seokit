<?php

declare(strict_types=1);

namespace Larament\SeoKit\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;
use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\Models\Seo;
use Larament\SeoKit\Support\Util;

/**
 * @phpstan-ignore trait.unused
 */
trait HasSeo
{
    /**
     * The relationship to the Seo model.
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'model');
    }

    /**
     * Prepares and applies SEO tags using the model's SEO data from the database.
     */
    public function prepareSeoTags(): void
    {
        if (! $data = $this->seoData()) {
            return;
        }

        SeoKit::fromSeoData(SeoData::fromArray($data));
    }

    /**
     * Get the SEO data from cache or database.
     */
    public function seoData(): ?array
    {
        return Cache::rememberForever(Util::modelCacheKey($this), fn () => $this->seo?->toArray());
    }

    /**
     * Check if the model is marked as cornerstone content.
     */
    public function isCornerstone(): bool
    {
        return (bool) ($this->seoData()['is_cornerstone'] ?? false);
    }

    /**
     * Boot the HasSeo trait.
     *
     * Clears the cached SEO data when the model is saved or deleted.
     * Ensures the SEO data is always up to date.
     */
    protected static function bootHasSeo(): void
    {
        static::deleted(function (Model $model): void {
            $model->seo()->delete();
            Cache::forget(Util::modelCacheKey($model));
        });
    }
}
