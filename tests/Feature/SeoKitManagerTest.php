<?php

declare(strict_types=1);

use Illuminate\Support\Facades\URL;
use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\JsonLD;
use Larament\SeoKit\MetaTags;
use Larament\SeoKit\OpenGraph;
use Larament\SeoKit\SeoKitManager;
use Larament\SeoKit\TwitterCards;

beforeEach(function () {
    config(['seokit.opengraph.enabled' => true]);
    config(['seokit.twitter.enabled' => true]);
    config(['seokit.jsonld.enabled' => true]);
});

it('can be resolved from the container', function () {
    expect(SeoKit::getFacadeRoot())->toBeInstanceOf(SeoKitManager::class);
});

it('provides access to meta tags service', function () {
    expect(SeoKit::meta())->toBeInstanceOf(MetaTags::class);
});

it('provides access to open graph service', function () {
    expect(SeoKit::opengraph())->toBeInstanceOf(OpenGraph::class);
});

it('provides access to twitter cards service', function () {
    expect(SeoKit::twitter())->toBeInstanceOf(TwitterCards::class);
});

it('provides access to json ld service', function () {
    expect(SeoKit::jsonld())->toBeInstanceOf(JsonLD::class);
});

it('can set title across all services', function () {
    SeoKit::title('Test Title');

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($metaHtml)->toContain('<title>Test Title</title>')
        ->and($ogHtml)->toContain('property="og:title" content="Test Title"')
        ->and($twitterHtml)->toContain('name="twitter:title" content="Test Title"');
});

it('can set description across all services', function () {
    SeoKit::description('Test Description');

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($metaHtml)->toContain('name="description" content="Test Description"')
        ->and($ogHtml)->toContain('property="og:description" content="Test Description"')
        ->and($twitterHtml)->toContain('name="twitter:description" content="Test Description"');
});

it('can set image for open graph and twitter', function () {
    SeoKit::image('https://example.com/image.jpg');

    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($ogHtml)->toContain('property="og:image" content="https://example.com/image.jpg"')
        ->and($twitterHtml)->toContain('name="twitter:image" content="https://example.com/image.jpg"');
});

it('can set canonical for meta tags and open graph', function () {
    SeoKit::canonical('https://example.com/canonical-page');

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();

    expect($metaHtml)->toContain('rel="canonical" href="https://example.com/canonical-page"')
        ->and($ogHtml)->toContain('property="og:url" content="https://example.com/canonical-page"');
});

it('can render all SEO tags to HTML', function () {
    SeoKit::meta()->title('Test Title');
    SeoKit::meta()->description('Test Description');
    SeoKit::opengraph()->type('website');
    SeoKit::opengraph()->siteName('Test Site');
    SeoKit::twitter()->card('summary');
    SeoKit::jsonld()->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Test Site',
    ]);

    $html = SeoKit::toHtml();

    expect($html)->toContain('<title>Test Title</title>')
        ->toContain('name="description" content="Test Description"')
        ->toContain('property="og:type" content="website"')
        ->toContain('property="og:site_name" content="Test Site"')
        ->toContain('name="twitter:card" content="summary"')
        ->toContain('Test Site')
        ->toContain('<script type="application/ld+json">');
});

it('can set SEO data from SeoData object', function () {
    $seoData = new SeoData(
        title: 'SEO Title',
        description: 'SEO Description',
        canonical: 'https://example.com/canonical',
        og_title: 'OG Title',
        og_description: 'OG Description',
        og_image: 'https://example.com/og-image.jpg',
        twitter_image: 'https://example.com/twitter-image.jpg',
        structured_data: [
            '@type' => 'WebSite',
            'name' => 'Test Site',
        ]
    );

    SeoKit::fromSeoData($seoData);

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();
    $jsonldArray = SeoKit::jsonld()->toArray();

    expect($metaHtml)
        ->toContain('<title>SEO Title</title>')
        ->toContain('content="SEO Description"')
        ->toContain('href="https://example.com/canonical"');

    expect($ogHtml)
        ->toContain('content="OG Title"')
        ->toContain('content="OG Description"')
        ->toContain('content="https://example.com/og-image.jpg"');

    expect($twitterHtml)
        ->toContain('content="OG Title"')
        ->toContain('content="OG Description"')
        ->toContain('content="https://example.com/twitter-image.jpg"');

    expect($jsonldArray)->toHaveCount(1);
    expect($jsonldArray[0]['@type'])->toBe('WebSite');
    expect($jsonldArray[0]['name'])->toBe('Test Site');
});

it('uses fallbacks when og fields are not provided in SeoData', function () {
    $seoData = new SeoData(
        title: 'Main Title',
        description: 'Main Description',
    );

    SeoKit::fromSeoData($seoData);

    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($ogHtml)
        ->toContain('content="Main Title"') // Uses title as fallback
        ->toContain('content="Main Description"'); // Uses description as fallback

    expect($twitterHtml)
        ->toContain('content="Main Title"') // Uses title as fallback
        ->toContain('content="Main Description"'); // Uses description as fallback
});

it('respects config settings when rendering HTML', function () {
    // We'll test this by temporarily changing config
    config(['seokit.opengraph.enabled' => false]);
    config(['seokit.twitter.enabled' => false]);
    config(['seokit.jsonld.enabled' => false]);

    SeoKit::meta()->title('Test Title');

    $html = SeoKit::toHtml();

    expect($html)
        ->toContain('<title>Test Title</title>') // Meta tags should always be included
        ->not->toContain('property="og:') // OpenGraph should be disabled
        ->not->toContain('name="twitter:') // Twitter should be disabled
        ->not->toContain('<script type="application/ld+json">'); // JSON-LD should be disabled
});

it('generates minified HTML when requested', function () {
    SeoKit::meta()->title('Test Title');
    SeoKit::opengraph()->type('website');
    SeoKit::twitter()->card('summary');
    SeoKit::jsonld()->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Test Site',
    ]);

    $html = SeoKit::toHtml(true); // minify = true
    // In a properly minified output, there should be no newlines between different tag types
    expect($html)->not->toContain("\n");
});

it('properly handles special characters across all services', function () {
    SeoKit::title('Title with & "Quotes" and <HTML>');
    SeoKit::description('Description with special chars: & < > " \'');

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($metaHtml)->toContain('Title with &amp; &quot;Quotes&quot; and &lt;HTML&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');

    expect($ogHtml)->toContain('Title with &amp; &quot;Quotes&quot; and &lt;HTML&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');

    expect($twitterHtml)->toContain('Title with &amp; &quot;Quotes&quot; and &lt;HTML&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');
});

it('handles UTF-8 characters across all services', function () {
    SeoKit::title('测试标题 Título Заголовок');
    SeoKit::description('Ümlaut ñ characters 日本語');

    $metaHtml = SeoKit::meta()->toHtml();
    $ogHtml = SeoKit::opengraph()->toHtml();
    $twitterHtml = SeoKit::twitter()->toHtml();

    expect($metaHtml)->toContain('测试标题 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');

    expect($ogHtml)->toContain('测试标题 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');

    expect($twitterHtml)->toContain('测试标题 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');
});

it('can set complex SeoData with all fields', function () {
    $seoData = new SeoData(
        title: 'Main Title',
        description: 'Main Description',
        canonical: 'https://example.com/page',
        robots: 'index, follow',
        og_title: 'OG Title',
        og_description: 'OG Description',
        og_image: 'https://example.com/og-image.jpg',
        twitter_image: 'https://example.com/twitter-image.jpg',
        structured_data: [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => 'Article Headline',
        ]
    );

    SeoKit::fromSeoData($seoData);

    $html = SeoKit::toHtml();

    expect($html)->toContain('<title>Main Title</title>')
        ->toContain('name="description" content="Main Description"')
        ->toContain('rel="canonical" href="https://example.com/page"')
        ->toContain('name="robots" content="index, follow"')
        ->toContain('property="og:title" content="OG Title"')
        ->toContain('property="og:description" content="OG Description"')
        ->toContain('property="og:image" content="https://example.com/og-image.jpg"')
        ->toContain('name="twitter:image" content="https://example.com/twitter-image.jpg"')
        ->toContain('"@type":"Article"');
});

it('can chain multiple method calls fluently', function () {
    $result = SeoKit::title('Test Title')
        ->description('Test Description')
        ->image('https://example.com/image.jpg');

    expect($result)->toBeInstanceOf(SeoKitManager::class);
});

it('handles SeoData with minimal required fields', function () {
    $seoData = new SeoData(
        title: 'Minimal Title',
        description: 'Minimal Description'
    );

    SeoKit::fromSeoData($seoData);

    $html = SeoKit::toHtml();
    expect($html)->toContain('<title>Minimal Title</title>')
        ->toContain('name="description" content="Minimal Description"')
        ->toBeString();
});

it('can override SeoData values after setting', function () {
    $seoData = new SeoData(
        title: 'Initial Title',
        description: 'Initial Description'
    );

    SeoKit::fromSeoData($seoData);
    SeoKit::title('Overridden Title');

    $metaHtml = SeoKit::meta()->toHtml();
    expect($metaHtml)->toContain('<title>Overridden Title</title>');
});

it('sets default meta with auto title from URL disabled', function () {
    config(['seokit.auto_title_from_url' => false]);
    config(['seokit.defaults.title' => 'Default Title']);
    config(['seokit.defaults.description' => 'Default Description']);
    config(['seokit.defaults.robots' => 'index,follow']);

    $html = SeoKit::meta()->toHtml();

    expect($html)->toContain('Default Title')
        ->toContain('Default Description')
        ->toContain('index,follow');
});

it('sets default meta with auto title from URL enabled', function () {
    config(['seokit.auto_title_from_url' => true]);
    config(['seokit.defaults.title' => null]);
    config(['app.name' => 'My App']);

    $html = SeoKit::meta()->toHtml();

    expect($html)->toContain('My App');
});

it('sets default canonical to current URL when null', function () {
    config(['seokit.defaults.canonical' => null]);

    URL::shouldReceive('current')
        ->andReturn('https://example.com/page');

    $html = SeoKit::meta()->toHtml();

    expect($html)->toContain('https://example.com/page');
});

it('sets default canonical to full URL when configured', function () {
    config(['seokit.defaults.canonical' => 'full']);

    URL::shouldReceive('full')->once()
        ->andReturn('https://example.com/page?query=value');
    URL::shouldReceive('current')
        ->andReturn('https://example.com/page');

    $html = SeoKit::meta()->toHtml();

    expect($html)->toContain('https://example.com/page?query=value');
});

it('does not set canonical when explicitly configured as other value', function () {
    config(['seokit.defaults.canonical' => false]);
    config(['seokit.opengraph.enabled' => true]);
    config(['seokit.opengraph.defaults.url' => false]);

    $html = SeoKit::meta()->toHtml();

    expect($html)->not->toContain('rel="canonical"');
});

it('sets default opengraph URL to current when null', function () {
    config(['seokit.opengraph.enabled' => true]);
    config(['seokit.opengraph.defaults.url' => null]);

    URL::shouldReceive('current')
        ->andReturn('https://example.com/page');

    $html = SeoKit::opengraph()->toHtml();

    expect($html)->toContain('https://example.com/page');
});

it('sets default opengraph URL to full when configured', function () {
    config(['seokit.opengraph.enabled' => true]);
    config(['seokit.opengraph.defaults.url' => 'full']);

    URL::shouldReceive('full')
        ->andReturn('https://example.com/page?query=value');
    URL::shouldReceive('current')
        ->andReturn('https://example.com/page');

    $html = SeoKit::opengraph()->toHtml();

    expect($html)->toContain('https://example.com/page?query=value');
});

it('sets default json ld from config', function () {
    config(['seokit.jsonld.enabled' => true]);
    config(['seokit.json_ld.defaults' => [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'My Company',
    ]]);

    $manager = app(SeoKitManager::class);

    $html = $manager->jsonld()->toHtml();

    expect($html)->toContain('"@type":"Organization"')
        ->toContain('"name":"My Company"');
});

it('blade directive renders seo tags', function () {
    SeoKit::title('Blade Test');

    $blade = Illuminate\Support\Facades\Blade::compileString('@seoKit');

    expect($blade)->toContain('app(\Larament\SeoKit\SeoKitManager::class)->toHtml');
});

it('blade directive accepts minify parameter', function () {
    $blade = Illuminate\Support\Facades\Blade::compileString('@seoKit(true)');

    expect($blade)->toContain('->toHtml');
});
