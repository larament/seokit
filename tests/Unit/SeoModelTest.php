<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Larament\SeoKit\Exceptions\InvalidImageUrlException;
use Larament\SeoKit\Models\Seo;
use Larament\SeoKit\Support\Util;

it('uses default table name from config', function (): void {
    config(['seokit.table_name' => 'seokit']);

    $seo = new Seo;

    expect($seo->getTable())->toBe('seokit');
});

it('uses custom table name from config', function (): void {
    config(['seokit.table_name' => 'custom_seo_table']);

    $seo = new Seo;

    expect($seo->getTable())->toBe('custom_seo_table');
});

it('falls back to default table name when config is null', function (): void {
    config(['seokit.table_name' => null]);

    $seo = new Seo;

    expect($seo->getTable())->toBe('seokit');
});

it('has morphTo relationship for model', function (): void {
    $seo = new Seo;

    $relation = $seo->model();

    expect($relation)->toBeInstanceOf(MorphTo::class);
});

it('casts structured_data as json', function (): void {
    $seo = new Seo;

    $casts = $seo->getCasts();

    expect($casts)->toHaveKey('structured_data')
        ->and($casts['structured_data'])->toBe('json');
});

it('casts is_cornerstone as boolean', function (): void {
    $seo = new Seo;

    $casts = $seo->getCasts();

    expect($casts)->toHaveKey('is_cornerstone')
        ->and($casts['is_cornerstone'])->toBe('boolean');
});

it('has no guarded attributes', function (): void {
    $seo = new Seo;

    expect($seo->getGuarded())->toBe([]);
});

it('can be instantiated', function (): void {
    $seo = new Seo;

    expect($seo)->toBeInstanceOf(Seo::class)
        ->and($seo)->toBeInstanceOf(Model::class);
});

it('resolves og_image_url using disk', function (): void {
    Storage::fake('public');

    $seo = new Seo([
        'og_image' => 'covers/og.png',
        'og_image_disk' => 'public',
    ]);

    expect($seo->og_image_url)->toBe(url('/storage/covers/og.png'));
});

it('throws exception for og_image_url when relative image has no disk in non-production', function (): void {
    $seo = new Seo([
        'og_image' => 'covers/og.png',
        'og_image_disk' => null,
    ]);

    expect(fn () => $seo->og_image_url)->toThrow(InvalidImageUrlException::class);
});

it('returns null for og_image_url when relative image has no disk in production', function (): void {
    $this->app->detectEnvironment(fn () => 'production');

    $seo = new Seo([
        'og_image' => 'covers/og.png',
        'og_image_disk' => null,
    ]);

    expect($seo->og_image_url)->toBeNull();
});

it('resolves og_image_url for external urls', function (): void {
    $seo = new Seo([
        'og_image' => 'https://example.com/banner.jpg',
    ]);

    expect($seo->og_image_url)->toBe('https://example.com/banner.jpg');
});

it('resolves twitter_image_url falling back to og_image and og_image_disk', function (): void {
    Storage::fake('s3');

    $seo = new Seo([
        'og_image' => 'covers/shared.png',
        'og_image_disk' => 's3',
    ]);

    expect($seo->twitter_image_url)->toContain('covers/shared.png');
});

it('resolves twitter_image_url with dedicated twitter_image and disk', function (): void {
    Storage::fake('public');

    $seo = new Seo([
        'og_image' => 'covers/og.png',
        'og_image_disk' => 's3',
        'twitter_image' => 'covers/twitter.png',
        'twitter_image_disk' => 'public',
    ]);

    expect($seo->twitter_image_url)->toBe(url('/storage/covers/twitter.png'));
});

it('does not throw when saving or deleting without associated model', function (): void {
    $seo = Seo::create([
        'title' => 'Orphan SEO',
        'model_type' => Seo::class,
        'model_id' => 999999,
    ]);

    expect($seo->exists)->toBeTrue();

    $seo->delete();
    expect($seo->exists)->toBeFalse();
});

it('clears model cache when attached seo record is deleted', function (): void {
    $seo = Seo::create([
        'title' => 'Cache Delete Test',
        'model_type' => Seo::class,
        'model_id' => 1,
    ]);

    // When model_id is 1, $seo->model resolves to the Seo record with id 1
    $cacheKey = Util::modelCacheKey($seo);
    Cache::put($cacheKey, ['title' => 'cached']);
    expect(Cache::has($cacheKey))->toBeTrue();

    $seo->delete();

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('auto-assigns configured default disk when saving relative og_image without disk', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'Default Disk Test',
        'model_type' => Seo::class,
        'model_id' => 10,
        'og_image' => 'covers/test.jpg',
    ]);

    expect($seo->og_image_disk)->toBe('public')
        ->and($seo->fresh()->og_image_disk)->toBe('public');
});

it('auto-assigns configured default disk when saving relative twitter_image without disk', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'Twitter Disk Test',
        'model_type' => Seo::class,
        'model_id' => 11,
        'twitter_image' => 'twitter/card.jpg',
    ]);

    expect($seo->twitter_image_disk)->toBe('public')
        ->and($seo->fresh()->twitter_image_disk)->toBe('public');
});

it('preserves explicit disk overrides when saving', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'Explicit Disk Test',
        'model_type' => Seo::class,
        'model_id' => 12,
        'og_image' => 'covers/s3-image.jpg',
        'og_image_disk' => 's3',
        'twitter_image' => 'twitter/custom-disk.jpg',
        'twitter_image_disk' => 'custom',
    ]);

    expect($seo->og_image_disk)->toBe('s3')
        ->and($seo->twitter_image_disk)->toBe('custom')
        ->and($seo->fresh()->og_image_disk)->toBe('s3')
        ->and($seo->fresh()->twitter_image_disk)->toBe('custom');
});

it('leaves disk as null when saving external URLs', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'External URL Test',
        'model_type' => Seo::class,
        'model_id' => 13,
        'og_image' => 'https://example.com/banner.jpg',
        'twitter_image' => 'http://example.com/card.png',
    ]);

    expect($seo->og_image_disk)->toBeNull()
        ->and($seo->twitter_image_disk)->toBeNull()
        ->and($seo->fresh()->og_image_disk)->toBeNull()
        ->and($seo->fresh()->twitter_image_disk)->toBeNull();
});

it('leaves disk as null when saving without image', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'No Image Test',
        'model_type' => Seo::class,
        'model_id' => 14,
    ]);

    expect($seo->og_image_disk)->toBeNull()
        ->and($seo->twitter_image_disk)->toBeNull()
        ->and($seo->fresh()->og_image_disk)->toBeNull()
        ->and($seo->fresh()->twitter_image_disk)->toBeNull();
});

it('resets disk to null when updating to external URL or clearing image', function (): void {
    config(['seokit.disk' => 'public']);

    $seo = Seo::create([
        'title' => 'Update Test',
        'model_type' => Seo::class,
        'model_id' => 14,
        'og_image' => 'covers/relative.jpg',
        'twitter_image' => 'twitter/relative.jpg',
    ]);

    expect($seo->og_image_disk)->toBe('public')
        ->and($seo->twitter_image_disk)->toBe('public');

    // Update og_image to external URL, clear twitter_image
    $seo->update([
        'og_image' => 'https://example.com/new-banner.jpg',
        'twitter_image' => null,
    ]);

    expect($seo->og_image_disk)->toBeNull()
        ->and($seo->twitter_image_disk)->toBeNull()
        ->and($seo->fresh()->og_image_disk)->toBeNull()
        ->and($seo->fresh()->twitter_image_disk)->toBeNull();
});

it('inherits application default filesystem when seokit.disk is null', function (): void {
    config([
        'seokit.disk' => null,
        'filesystems.default' => 'custom-app-disk',
    ]);

    $seo = Seo::create([
        'title' => 'Inherit App Disk Test',
        'model_type' => Seo::class,
        'model_id' => 15,
        'og_image' => 'covers/inherited.jpg',
    ]);

    expect($seo->og_image_disk)->toBe('custom-app-disk')
        ->and($seo->fresh()->og_image_disk)->toBe('custom-app-disk');
});

it('prioritizes seokit.disk over filesystems.default', function (): void {
    config([
        'seokit.disk' => 'custom-seo-disk',
        'filesystems.default' => 'custom-app-disk',
    ]);

    $seo = Seo::create([
        'title' => 'Priority Test',
        'model_type' => Seo::class,
        'model_id' => 16,
        'og_image' => 'covers/priority.jpg',
    ]);

    expect($seo->og_image_disk)->toBe('custom-seo-disk')
        ->and($seo->fresh()->og_image_disk)->toBe('custom-seo-disk');
});

it('does not assign disk if both seokit.disk and filesystems.default are null', function (): void {
    config([
        'seokit.disk' => null,
        'filesystems.default' => null,
    ]);

    $seo = Seo::create([
        'title' => 'Null Config Test',
        'model_type' => Seo::class,
        'model_id' => 17,
        'og_image' => 'covers/unconfigured.jpg',
    ]);

    expect($seo->og_image_disk)->toBeNull()
        ->and($seo->fresh()->og_image_disk)->toBeNull();
});
