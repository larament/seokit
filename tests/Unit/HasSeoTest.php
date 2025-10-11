<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Larament\SeoKit\Concerns\HasSeo;
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\Models\Seo;
use Larament\SeoKit\Support\Util;

beforeEach(function () {
    // Create test tables
    Schema::create('test_posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->timestamps();
    });

    // Define test model
    $this->testModel = new class extends Model
    {
        use HasSeo;

        protected $table = 'test_posts';

        protected $guarded = [];
    };
});

afterEach(function () {
    Schema::dropIfExists('test_posts');
    Cache::flush();
});

it('has seo morphOne relationship', function () {
    $relation = $this->testModel->seo();

    expect($relation)->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\MorphOne::class)
        ->and($relation->getRelated())->toBeInstanceOf(Seo::class);
});

it('prepares seo tags when seo data exists', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'SEO Title for post',
        'description' => 'SEO Description for post',
        'canonical' => 'https://example.com/test',
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('SEO Title')
        ->and($html)->toContain('SEO Description')
        ->and($html)->toContain('https://example.com/test');
});

it('does not prepare seo tags when seo data is null', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    // No SEO data attached
    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    // Should not throw error
    expect($html)->toContain('<title>');
});

it('retrieves seo data from cache', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Cached Title',
        'description' => 'Cached Description',
    ]);

    // First call - should cache
    $data1 = $post->seoData();

    // Second call - should retrieve from cache
    $data2 = $post->seoData();

    expect($data1)->toBe($data2)
        ->and($data1['title'])->toBe('Cached Title')
        ->and($data1['description'])->toBe('Cached Description');
});

it('returns null when seo relationship does not exist', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $result = $post->seoData();

    expect($result)->toBeNull();
});

it('checks if model is cornerstone content', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Cornerstone',
        'is_cornerstone' => true,
    ]);

    expect($post->isCornerstone())->toBeTrue();
});

it('returns false when is_cornerstone is not set', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Regular Post',
    ]);

    expect($post->isCornerstone())->toBeFalse();
});

it('returns false when is_cornerstone is false', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Not Cornerstone',
        'is_cornerstone' => false,
    ]);

    expect($post->isCornerstone())->toBeFalse();
});

it('returns false when seo data is null', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    expect($post->isCornerstone())->toBeFalse();
});

it('clears cache when model is saved', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Original Title',
    ]);

    // Cache the data
    $post->seoData();

    // Update SEO data
    $post->seo->update(['title' => 'Updated Title']);

    // Fresh data should reflect the update
    $freshData = $post->fresh()->seoData();

    expect($freshData['title'])->toBe('Updated Title');
});

it('clears cache when model is deleted', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'To Be Deleted',
    ]);

    $cacheKey = Util::modelCacheKey($post);

    // Cache the data
    $post->seoData();
    expect(Cache::has($cacheKey))->toBeTrue();

    // Delete the post - should clear cache
    $post->delete();

    expect(Cache::has($cacheKey))->toBeFalse();
});

it('generates correct cache key for seo data', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create(['title' => 'Test']);

    $expectedKey = Util::modelCacheKey($post);

    $post->seoData();

    expect(Cache::has($expectedKey))->toBeTrue();
});

it('prepares seo tags with complex data', function () {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Complex SEO',
        'description' => 'Complex Description',
        'og_title' => 'OG Title',
        'og_description' => 'OG Description',
        'og_image' => 'https://example.com/image.jpg',
        'twitter_image' => 'https://example.com/twitter.jpg',
        'structured_data' => [
            '@type' => 'Article',
            'headline' => 'Article Headline',
        ],
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Complex SEO')
        ->and($html)->toContain('OG Title')
        ->and($html)->toContain('https://example.com/image.jpg')
        ->and($html)->toContain('Article Headline');
});
