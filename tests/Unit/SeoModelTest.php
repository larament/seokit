<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Larament\SeoKit\Exceptions\InvalidImageUrlException;
use Larament\SeoKit\Models\Seo;

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
