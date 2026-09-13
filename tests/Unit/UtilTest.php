<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Larament\SeoKit\Exceptions\InvalidImageUrlException;
use Larament\SeoKit\Support\Util;
use Larament\SeoKit\Tests\Fixtures\Http\Middleware\DummyInertiaMiddleware;

function fakeRoute(): Illuminate\Routing\Route
{
    return new Illuminate\Routing\Route('GET', '/test', fn () => 'ok');
}

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
    $model = new class extends Model
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

it('detects inertia route when middleware is present', function (): void {
    $route = fakeRoute();
    Route::partialMock()
        ->shouldReceive('current')
        ->andReturn($route);

    Route::partialMock()
        ->shouldReceive('gatherRouteMiddleware')
        ->with($route)
        ->andReturn([
            'web',
            DummyInertiaMiddleware::class,
        ]);

    expect(Util::isInertiaRoute())->toBeTrue();
});

it('does not detect inertia route when middleware is absent', function (): void {
    $route = fakeRoute();
    Route::partialMock()
        ->shouldReceive('current')
        ->andReturn($route);

    Route::partialMock()
        ->shouldReceive('gatherRouteMiddleware')
        ->with($route)
        ->andReturn([
            'web',
            'auth',
        ]);

    expect(Util::isInertiaRoute())->toBeFalse();
});

it('does not detect inertia route when current route is null', function (): void {
    Route::partialMock()
        ->shouldReceive('current')
        ->andReturn(null);

    expect(Util::isInertiaRoute())->toBeFalse();
});

it('handles closure in route middleware', function (): void {
    $route = fakeRoute();
    Route::partialMock()
        ->shouldReceive('current')
        ->andReturn($route);

    Route::partialMock()
        ->shouldReceive('gatherRouteMiddleware')
        ->with($route)
        ->andReturn([
            'web',
            fn () => 'closure',
        ]);

    expect(Util::isInertiaRoute())->toBeFalse();
});

it('returns null when image is null or empty', function (): void {
    expect(Util::resolveImageUrl(null))->toBeNull()
        ->and(Util::resolveImageUrl(''))->toBeNull()
        ->and(Util::resolveImageUrl('   '))->toBeNull();
});

it('returns external URLs unchanged', function (): void {
    expect(Util::resolveImageUrl('https://example.com/image.jpg'))->toBe('https://example.com/image.jpg')
        ->and(Util::resolveImageUrl('http://example.com/image.png'))->toBe('http://example.com/image.png');
});

it('throws an exception for relative path when disk is not specified in non-production', function (): void {
    expect(fn () => Util::resolveImageUrl('covers/og.jpg'))
        ->toThrow(InvalidImageUrlException::class, 'The image path [covers/og.jpg] cannot be resolved because no storage disk was specified.');
});

it('logs an error and returns null for relative path when disk is not specified in production', function (): void {
    $this->app->detectEnvironment(fn () => 'production');
    Log::shouldReceive('error')
        ->once()
        ->with('The image path [covers/og.jpg] cannot be resolved because no storage disk was specified.');

    $url = Util::resolveImageUrl('covers/og.jpg');

    expect($url)->toBeNull();
});

it('resolves relative path using explicit disk', function (): void {
    Storage::fake('s3');

    $url = Util::resolveImageUrl('covers/og.jpg', 's3');

    expect($url)->toContain('covers/og.jpg');
});

it('ensures storage url is absolute when disk returns relative url', function (): void {
    Storage::fake('public');

    $url = Util::resolveImageUrl('seo/banner.png', 'public');

    expect($url)->toBe(url('/storage/seo/banner.png'));
});

it('returns storage url unchanged when disk returns an absolute url', function (): void {
    config(['filesystems.disks.custom' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'url' => 'https://cdn.example.com/assets',
    ]]);

    $url = Util::resolveImageUrl('banner.png', 'custom');

    expect($url)->toBe('https://cdn.example.com/assets/banner.png');
});
