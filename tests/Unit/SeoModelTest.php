<?php

declare(strict_types=1);

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

    expect($relation)->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\MorphTo::class);
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
        ->and($seo)->toBeInstanceOf(Illuminate\Database\Eloquent\Model::class);
});
