<?php

declare(strict_types=1);

use Larament\SeoKit\Facades\SeoKit;

it('can add a schema to the collection', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Larament.com',
    ]);

    $html = $jsonld->toHtml();
    expect($html)->toContain('<script type="application/ld+json">')
        ->toContain('"@context":"https://schema.org"')
        ->toContain('"@type":"WebSite"')
        ->toContain('"name":"Larament.com"');
});

it('can remove a schema by index', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add(['@type' => 'WebSite', 'name' => 'Larament.com']);
    $jsonld->add(['@type' => 'WebSite', 'name' => 'Digimax.it.com']);

    expect($jsonld->toArray())->toHaveCount(2);

    $jsonld->remove(0);
    $array = $jsonld->toArray();
    // After removal, indices are renumbered or maintained depending on implementation
    // Let's check the remaining items
    expect($array)->toHaveCount(1);
    // The remaining item should be the one at original index 1
    expect(array_values($array)[0]['name'])->toBe('Digimax.it.com');
});

it('can add a WebSite schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->website([
        'name' => "Raziul's Blog",
        'url' => 'https://raziul.dev',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('WebSite')
        ->and($schema['name'])->toBe("Raziul's Blog")
        ->and($schema['url'])->toBe('https://raziul.dev');
});

it('can add an Organization schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->organization([
        'name' => 'Larament',
        'url' => 'https://larament.com',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('Organization')
        ->and($schema['name'])->toBe('Larament')
        ->and($schema['url'])->toBe('https://larament.com');
});

it('can add a Person schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->person([
        'name' => 'Raziul Islam',
        'jobTitle' => 'Developer',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('Person')
        ->and($schema['name'])->toBe('Raziul Islam')
        ->and($schema['jobTitle'])->toBe('Developer');
});

it('can add an Article schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->article([
        'headline' => 'Larament is a complete SEO package for Laravel.',
        'description' => 'Test Description',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('Article')
        ->and($schema['headline'])->toBe('Larament is a complete SEO package for Laravel.')
        ->and($schema['description'])->toBe('Test Description');
});

it('can add a BlogPosting schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->blogPosting([
        'headline' => 'Test Blog Post',
        'description' => 'Test Blog Description',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('BlogPosting')
        ->and($schema['headline'])->toBe('Test Blog Post')
        ->and($schema['description'])->toBe('Test Blog Description');
});

it('can add a Product schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->product([
        'name' => 'Test Product',
        'description' => 'Test Product Description',
        'offers' => [
            '@type' => 'Offer',
            'price' => '99.99',
            'priceCurrency' => 'USD',
        ],
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('Product')
        ->and($schema['name'])->toBe('Test Product')
        ->and($schema['description'])->toBe('Test Product Description');
});

it('can add a LocalBusiness schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->localBusiness([
        'name' => 'Digimax',
        'description' => 'Digimax is a web development company.',
        'telephone' => '+8801782802304',
    ]);

    $array = $jsonld->toArray();
    $schema = $array[0];

    expect($schema['@type'])->toBe('LocalBusiness')
        ->and($schema['name'])->toBe('Digimax')
        ->and($schema['description'])->toBe('Digimax is a web development company.')
        ->and($schema['telephone'])->toBe('+8801782802304');
});

it('can clear all schemas', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add(['@type' => 'WebSite', 'name' => 'Test Site']);

    expect($jsonld->toArray())->not->toBeEmpty();

    $jsonld->clear();
    expect($jsonld->toArray())->toBeEmpty();
});

it('converts to array properly', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add(['@type' => 'WebSite', 'name' => 'Test Site']);
    $jsonld->add(['@type' => 'Organization', 'name' => 'Test Org']);

    $array = $jsonld->toArray();
    expect($array)->toHaveCount(2)
        ->and($array[0]['name'])->toBe('Test Site')
        ->and($array[1]['name'])->toBe('Test Org');
});

it('generates HTML with script tags properly', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Test Site',
    ]);

    $html = $jsonld->toHtml();
    expect($html)->toContain('<script type="application/ld+json">')
        ->toContain('{"@context":"https://schema.org","@type":"WebSite","name":"Test Site"}')
        ->toContain('</script>');
});

it('returns empty string when no schemas exist', function () {
    $jsonld = SeoKit::jsonld();

    $html = $jsonld->toHtml();
    expect($html)->toBe('');
});

it('generates minified HTML when requested', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Test Site',
    ]);

    $html = $jsonld->toHtml(true); // minify = true
    // JSON should be minified and no newlines from multiple schemas
    expect($html)->not->toContain("\n");
});

it('properly handles special characters in JSON', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@type' => 'WebSite',
        'name' => 'Test & Site with "Quotes" and <HTML>',
        'description' => 'Special chars: & < > " \'',
    ]);

    $html = $jsonld->toHtml();
    // JSON should properly escape these
    expect($html)->toContain('"name":"Test & Site with \\"Quotes\\" and <HTML>"');
});

it('handles UTF-8 characters correctly', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@type' => 'WebSite',
        'name' => '测试网站 Título Заголовок',
        'description' => 'Ümlaut ñ characters 日本語',
    ]);

    $html = $jsonld->toHtml();
    expect($html)->toContain('测试网站 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');
});

it('handles nested arrays and objects', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@type' => 'Product',
        'name' => 'Test Product',
        'offers' => [
            '@type' => 'Offer',
            'price' => '99.99',
            'availability' => [
                '@type' => 'ItemAvailability',
                'name' => 'InStock',
            ],
        ],
    ]);

    $array = $jsonld->toArray();
    expect($array[0]['offers']['availability']['name'])->toBe('InStock');
});

it('can add multiple schemas and maintain order', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->website(['name' => 'Site 1']);
    $jsonld->organization(['name' => 'Org 1']);
    $jsonld->person(['name' => 'Person 1']);

    $array = $jsonld->toArray();
    expect($array)->toHaveCount(3)
        ->and($array[0]['@type'])->toBe('WebSite')
        ->and($array[1]['@type'])->toBe('Organization')
        ->and($array[2]['@type'])->toBe('Person');
});

it('can add BreadcrumbList schema', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => 'https://example.com',
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Category',
                'item' => 'https://example.com/category',
            ],
        ],
    ]);

    $array = $jsonld->toArray();
    expect($array[0]['@type'])->toBe('BreadcrumbList')
        ->and($array[0]['itemListElement'])->toHaveCount(2);
});

it('validates JSON structure', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Test Site',
    ]);

    $html = $jsonld->toHtml();
    $scriptContent = preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    expect($scriptContent)->toBe(1);
    // Verify it's valid JSON
    $decoded = json_decode($matches[1], true);
    expect($decoded)->toBeArray()
        ->and($decoded['@type'])->toBe('WebSite');
});

it('handles empty schema gracefully', function () {
    $jsonld = SeoKit::jsonld();
    $jsonld->add([]);

    $array = $jsonld->toArray();
    expect($array)->toHaveCount(1);
});
