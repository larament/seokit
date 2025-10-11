<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Larament\SeoKit\Concerns\HasSeoData;
use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Facades\SeoKit;

beforeEach(function (): void {
    // Create test table
    Schema::create('test_articles', function (Blueprint $table): void {
        $table->id();
        $table->string('title');
        $table->text('content')->nullable();
        $table->string('featured_image')->nullable();
        $table->timestamps();
    });

    // Create test model
    $this->model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $guarded = [];

        public function toSeoData(): SeoData
        {
            return new SeoData(
                title: 'Test Title',
                description: 'Test Description',
                canonical: 'https://example.com/article',
                og_title: 'OG Title',
                og_description: 'OG Description',
                og_image: 'https://example.com/image.jpg',
                twitter_image: 'https://example.com/twitter.jpg',
                structured_data: ['@type' => 'Article']
            );
        }
    };
});

afterEach(function (): void {
    Schema::dropIfExists('test_articles');
});

it('prepares seo tags from toSeoData method', function (): void {
    $article = $this->model->create(['title' => 'Article']);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Test Title')
        ->and($html)->toContain('Test Description');
});

it('handles empty seo data gracefully', function (): void {
    $model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $guarded = [];

        public function toSeoData(): SeoData
        {
            return new SeoData;
        }
    };

    $article = $model->create(['title' => 'Article']);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();
    expect($html)->not->toContain('Test Title');
});

it('passes correct seo data to manager', function (): void {
    $model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $guarded = [];

        public function toSeoData(): SeoData
        {
            return new SeoData(
                title: 'Article Title',
                description: 'Article Description',
                canonical: 'https://example.com/article',
                og_title: 'OG Title',
                og_image: 'https://example.com/image.jpg'
            );
        }
    };

    $article = $model->create(['title' => 'Article']);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Article Title')
        ->and($html)->toContain('Article Description')
        ->and($html)->toContain('https://example.com/article')
        ->and($html)->toContain('OG Title')
        ->and($html)->toContain('https://example.com/image.jpg');
});

it('can use dynamic data from model attributes', function (): void {
    $model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $fillable = ['title', 'content', 'featured_image'];

        private function toSeoData(): SeoData
        {
            return new SeoData(
                title: $this->title,
                description: mb_substr($this->content ?? '', 0, 160),
                og_image: $this->featured_image
            );
        }
    };

    $article = $model->create([
        'title' => 'Dynamic Title',
        'content' => 'This is the content that will be used as description',
        'featured_image' => 'https://example.com/featured.jpg',
    ]);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Dynamic Title')
        ->and($html)->toContain('This is the content that will be used as description')
        ->and($html)->toContain('https://example.com/featured.jpg');
});

it('works with complex seo data including structured data', function (): void {
    $model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $guarded = [];

        private function toSeoData(): SeoData
        {
            return new SeoData(
                title: 'Article',
                description: 'Description',
                structured_data: [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => 'Article Headline',
                ]
            );
        }
    };

    $article = $model->create(['title' => 'Article']);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Article')
        ->and($html)->toContain('Description')
        ->and($html)->toContain('"@type":"Article"')
        ->and($html)->toContain('Article Headline');
});

it('prepares seo tags with all possible fields', function (): void {
    $model = new class extends Model
    {
        use HasSeoData;

        protected $table = 'test_articles';

        protected $guarded = [];

        private function toSeoData(): SeoData
        {
            return new SeoData(
                title: 'Full Title',
                description: 'Full Description',
                canonical: 'https://example.com/full',
                robots: 'index, follow',
                og_title: 'Full OG Title',
                og_description: 'Full OG Description',
                og_image: 'https://example.com/og.jpg',
                twitter_image: 'https://example.com/twitter.jpg',
                structured_data: ['@type' => 'WebPage']
            );
        }
    };

    $article = $model->create(['title' => 'Article']);

    $article->prepareSeoTags();

    $html = SeoKit::toHtml();

    expect($html)->toContain('Full Title')
        ->and($html)->toContain('Full Description')
        ->and($html)->toContain('https://example.com/full')
        ->and($html)->toContain('index, follow')
        ->and($html)->toContain('Full OG Title')
        ->and($html)->toContain('Full OG Description')
        ->and($html)->toContain('https://example.com/og.jpg')
        ->and($html)->toContain('https://example.com/twitter.jpg');
});
