<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Larament\SeoKit\Concerns\HasSeo;
use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\Models\Seo;
use Larament\SeoKit\Support\Util;

beforeEach(function (): void {
    // Create test tables
    Schema::create('test_posts', function (Blueprint $table): void {
        $table->id();
        $table->string('title');
        $table->string('name')->nullable();
        $table->text('description')->nullable();
        $table->text('excerpt')->nullable();
        $table->text('summary')->nullable();
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

afterEach(function (): void {
    Schema::dropIfExists('test_posts');
    Cache::flush();
});

it('has seo morphOne relationship', function (): void {
    $relation = $this->testModel->seo();

    expect($relation)->toBeInstanceOf(MorphOne::class)
        ->and($relation->getRelated())->toBeInstanceOf(Seo::class);
});

it('prepares seo tags when seo data exists', function (): void {
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

it('falls back to model attributes when seo fields are empty', function (): void {
    $model = new class extends Model
    {
        use HasSeo;

        protected $table = 'test_posts';

        protected $guarded = [];

        protected function fallbackSeoData(): SeoData
        {
            return new SeoData(
                title: $this->title,
                description: $this->description,
            );
        }
    };

    $post = $model->create(['title' => 'Fallback Post Title', 'description' => 'Fallback post description']);

    $post->seo()->create([
        'title' => '',
        'description' => '',
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Fallback Post Title')
        ->and($html)->toContain('Fallback post description');
});

it('uses user-defined fallback values', function (): void {
    $model = new class extends Model
    {
        use HasSeo;

        protected $table = 'test_posts';

        protected $guarded = [];

        protected function fallbackSeoData(): SeoData
        {
            return new SeoData(
                title: $this->name,
                description: $this->excerpt,
            );
        }
    };

    $post = $model->create([
        'title' => 'unused-title',
        'name' => 'Fallback Name Title',
        'excerpt' => 'Fallback excerpt description',
    ]);

    $post->seo()->create([
        'title' => '',
        'description' => '',
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Fallback Name Title')
        ->and($html)->toContain('Fallback excerpt description');
});

it('keeps explicit seo field values over fallback values', function (): void {
    $post = $this->testModel->create([
        'title' => 'Model Title',
        'description' => 'Model Description',
    ]);

    $post->seo()->create([
        'title' => 'Explicit SEO Title',
        'description' => 'Explicit SEO Description',
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Explicit SEO Title')
        ->and($html)->toContain('Explicit SEO Description')
        ->and($html)->not->toContain('<title>Model Title');
});

it('does not apply fallback when model does not define seoFallbackData', function (): void {
    $post = $this->testModel->create([
        'title' => 'Model Title',
        'description' => 'Model Description',
    ]);

    $post->seo()->create([
        'title' => '',
        'description' => '',
    ]);

    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->not->toContain('Model Title')
        ->and($html)->not->toContain('Model Description');
});

it('does not prepare seo tags when seo data is null', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    // No SEO data attached
    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    // Should not throw error
    expect($html)->toContain('<title>');
});

it('prepares seo tags from fallback when seo relationship is missing', function (): void {
    $model = new class extends Model
    {
        use HasSeo;

        protected $table = 'test_posts';

        protected $guarded = [];

        protected function fallbackSeoData(): SeoData
        {
            return new SeoData(
                title: $this->title,
                description: $this->excerpt,
            );
        }
    };

    $post = $model->create([
        'title' => 'Fallback Only Title',
        'excerpt' => 'Fallback only description',
    ]);

    // No SEO relationship row
    $post->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Fallback Only Title')
        ->and($html)->toContain('Fallback only description');
});

it('retrieves seo data from cache', function (): void {
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

it('returns null when seo relationship does not exist', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $result = $post->seoData();

    expect($result)->toBeNull();
});

it('checks if model is cornerstone content', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Cornerstone',
        'is_cornerstone' => true,
    ]);

    expect($post->isCornerstone())->toBeTrue();
});

it('returns false when is_cornerstone is not set', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Regular Post',
    ]);

    expect($post->isCornerstone())->toBeFalse();
});

it('returns false when is_cornerstone is false', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create([
        'title' => 'Not Cornerstone',
        'is_cornerstone' => false,
    ]);

    expect($post->isCornerstone())->toBeFalse();
});

it('returns false when seo data is null', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    expect($post->isCornerstone())->toBeFalse();
});

it('clears cache when model is saved', function (): void {
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

it('clears cache when model is deleted', function (): void {
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

it('generates correct cache key for seo data', function (): void {
    $post = $this->testModel->create(['title' => 'Test Post']);

    $post->seo()->create(['title' => 'Test']);

    $expectedKey = Util::modelCacheKey($post);

    $post->seoData();

    expect(Cache::has($expectedKey))->toBeTrue();
});

it('prepares seo tags with complex data', function (): void {
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
