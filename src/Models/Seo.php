<?php

declare(strict_types=1);

namespace Larament\SeoKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Larament\SeoKit\Support\Util;

/**
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $canonical
 * @property string|null $robots
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $og_image_disk
 * @property string|null $twitter_image
 * @property string|null $twitter_image_disk
 * @property array<string, mixed>|null $structured_data
 * @property bool $is_cornerstone
 * @property-read string|null $og_image_url
 * @property-read string|null $twitter_image_url
 */
final class Seo extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('seokit.table_name') ?: 'seokit';
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        self::saved(function (Seo $seo): void {
            Cache::forget(Util::modelCacheKey($seo->model));
        });
    }

    protected function casts(): array
    {
        return [
            'structured_data' => 'json',
            'is_cornerstone' => 'boolean',
        ];
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return Util::resolveImageUrl($this->og_image, $this->og_image_disk);
    }

    public function getTwitterImageUrlAttribute(): ?string
    {
        $image = $this->twitter_image ?? $this->og_image;
        $disk = $this->twitter_image ? $this->twitter_image_disk : $this->og_image_disk;

        return Util::resolveImageUrl($image, $disk);
    }
}
