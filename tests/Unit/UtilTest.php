<?php

declare(strict_types=1);

use Larament\SeoKit\Support\Util;

it('affixes title with before and after', function (): void {
    config(['seokit.defaults.before_title' => 'Prefix']);
    config(['seokit.defaults.after_title' => 'Suffix']);
    config(['seokit.defaults.title_separator' => ' | ']);

    $result = Util::affixTitle('Page Title');

    expect($result)->toBe('Prefix | Page Title | Suffix');
});

it('returns title unchanged when no affix configured', function (): void {
    config(['seokit.defaults.before_title' => null]);
    config(['seokit.defaults.after_title' => null]);

    $result = Util::affixTitle('Page Title');

    expect($result)->toBe('Page Title');
});

it('affixes only before title', function (): void {
    config(['seokit.defaults.before_title' => 'Prefix']);
    config(['seokit.defaults.after_title' => null]);
    config(['seokit.defaults.title_separator' => ' - ']);

    $result = Util::affixTitle('Page Title');

    expect($result)->toBe('Prefix - Page Title');
});

it('affixes only after title', function (): void {
    config(['seokit.defaults.before_title' => null]);
    config(['seokit.defaults.after_title' => 'Suffix']);
    config(['seokit.defaults.title_separator' => ' | ']);

    $result = Util::affixTitle('Page Title');

    expect($result)->toBe('Page Title | Suffix');
});

it('handles closure for before title', function (): void {
    config(['seokit.defaults.before_title' => fn ($title): string => 'Dynamic']);
    config(['seokit.defaults.after_title' => null]);
    config(['seokit.defaults.title_separator' => ' - ']);

    $result = Util::affixTitle('Page Title');

    expect($result)->toBe('Dynamic - Page Title');
});

it('cleans string by removing html tags', function (): void {
    $dirty = '<script>alert("xss")</script>Hello World';

    $result = Util::cleanString($dirty);

    expect($result)->not->toContain('<script>')
        ->and($result)->toContain('Hello World');
});

it('cleans string by removing http-equiv', function (): void {
    $dirty = 'http-equiv=refresh url=http://example.com';

    $result = Util::cleanString($dirty);

    expect($result)->not->toContain('http-equiv')
        ->and($result)->not->toContain('url=');
});

it('escapes special characters in clean string', function (): void {
    $dirty = '<b>Bold & "quoted"</b>';

    $result = Util::cleanString($dirty);

    expect($result)->toContain('&amp;')
        ->toContain('&quot;')
        ->and($result)->not->toContain('<b>');
});

it('generates unique model cache key', function (): void {
    $model = new class extends Illuminate\Database\Eloquent\Model
    {
        protected $table = 'posts';

        public function getKey()
        {
            return 123;
        }

        public function getMorphClass()
        {
            return 'App\\Models\\Post';
        }
    };

    $result = Util::modelCacheKey($model);

    expect($result)->toBe('seokit.App.Models.Post.123');
});

it('gets title from URL when path is root', function (): void {
    config(['app.name' => 'My Application']);

    $this->get('/');

    $result = Util::getTitleFromUrl();

    expect($result)->toBe('My Application');
});

it('gets title from URL by converting slug to headline', function (): void {
    config(['seokit.title_inference_callback' => null]);

    $this->get('/blog/my-awesome-post');

    $result = Util::getTitleFromUrl();

    expect($result)->toBe('My Awesome Post');
});

it('gets title from URL using custom callback', function (): void {
    config(['seokit.title_inference_callback' => fn ($slug) => mb_strtoupper((string) $slug)]);

    $this->get('/blog/test-page');

    $result = Util::getTitleFromUrl();

    expect($result)->toBe('TEST-PAGE');
});

it('handles URL with file extension when getting title', function (): void {
    config(['seokit.title_inference_callback' => null]);

    $this->get('/pages/contact-us.html');

    $result = Util::getTitleFromUrl();

    expect($result)->toBe('Contact Us');
});
