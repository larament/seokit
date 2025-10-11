<?php

declare(strict_types=1);

namespace Larament\SeoKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Larament\SeoKit\Support\Util;

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
}
