<?php

declare(strict_types=1);

use Larament\SeoKit\Data\SeoData;

it('can be created with all fields', function (): void {
    $seoData = new SeoData(
        title: 'Test Title',
        description: 'Test Description',
        canonical: 'https://example.com/page',
        robots: 'index, follow',
        og_title: 'OG Title',
        og_description: 'OG Description',
        og_image: 'https://example.com/og-image.jpg',
        twitter_image: 'https://example.com/twitter-image.jpg',
        structured_data: ['@type' => 'WebSite', 'name' => 'Test Site']
    );

    expect($seoData->title)->toBe('Test Title')
        ->and($seoData->description)->toBe('Test Description')
        ->and($seoData->canonical)->toBe('https://example.com/page')
        ->and($seoData->robots)->toBe('index, follow')
        ->and($seoData->og_title)->toBe('OG Title')
        ->and($seoData->og_description)->toBe('OG Description')
        ->and($seoData->og_image)->toBe('https://example.com/og-image.jpg')
        ->and($seoData->twitter_image)->toBe('https://example.com/twitter-image.jpg')
        ->and($seoData->structured_data)->toBe(['@type' => 'WebSite', 'name' => 'Test Site']);
});

it('can be created with minimal fields', function (): void {
    $seoData = new SeoData(
        title: 'Test Title',
        description: 'Test Description'
    );

    expect($seoData->title)->toBe('Test Title')
        ->and($seoData->description)->toBe('Test Description')
        ->and($seoData->canonical)->toBeNull()
        ->and($seoData->robots)->toBeNull()
        ->and($seoData->og_title)->toBe('')
        ->and($seoData->og_description)->toBe('')
        ->and($seoData->og_image)->toBe('')
        ->and($seoData->twitter_image)->toBeNull()
        ->and($seoData->structured_data)->toBeNull();
});

it('can be created with no fields', function (): void {
    $seoData = new SeoData;

    expect($seoData->title)->toBe('')
        ->and($seoData->description)->toBe('')
        ->and($seoData->canonical)->toBeNull()
        ->and($seoData->robots)->toBeNull()
        ->and($seoData->og_title)->toBe('')
        ->and($seoData->og_description)->toBe('')
        ->and($seoData->og_image)->toBe('')
        ->and($seoData->twitter_image)->toBeNull()
        ->and($seoData->structured_data)->toBeNull();
});

it('can be created from array with all fields', function (): void {
    $data = [
        'title' => 'Array Title',
        'description' => 'Array Description',
        'canonical' => 'https://example.com/canonical',
        'robots' => 'noindex, nofollow',
        'og_title' => 'Array OG Title',
        'og_description' => 'Array OG Description',
        'og_image' => 'https://example.com/og.jpg',
        'twitter_image' => 'https://example.com/twitter.jpg',
        'structured_data' => ['@type' => 'Article', 'headline' => 'Article'],
    ];

    $seoData = SeoData::fromArray($data);

    expect($seoData->title)->toBe('Array Title')
        ->and($seoData->description)->toBe('Array Description')
        ->and($seoData->canonical)->toBe('https://example.com/canonical')
        ->and($seoData->robots)->toBe('noindex, nofollow')
        ->and($seoData->og_title)->toBe('Array OG Title')
        ->and($seoData->og_description)->toBe('Array OG Description')
        ->and($seoData->og_image)->toBe('https://example.com/og.jpg')
        ->and($seoData->twitter_image)->toBe('https://example.com/twitter.jpg')
        ->and($seoData->structured_data)->toBe(['@type' => 'Article', 'headline' => 'Article']);
});

it('can be created from array with partial fields', function (): void {
    $data = [
        'title' => 'Partial Title',
        'og_image' => 'https://example.com/image.jpg',
    ];

    $seoData = SeoData::fromArray($data);

    expect($seoData->title)->toBe('Partial Title')
        ->and($seoData->description)->toBe('')
        ->and($seoData->canonical)->toBeNull()
        ->and($seoData->robots)->toBeNull()
        ->and($seoData->og_title)->toBe('')
        ->and($seoData->og_description)->toBe('')
        ->and($seoData->og_image)->toBe('https://example.com/image.jpg')
        ->and($seoData->twitter_image)->toBeNull()
        ->and($seoData->structured_data)->toBeNull();
});

it('can be created from empty array', function (): void {
    $seoData = SeoData::fromArray([]);

    expect($seoData->title)->toBe('')
        ->and($seoData->description)->toBe('')
        ->and($seoData->canonical)->toBeNull()
        ->and($seoData->robots)->toBeNull()
        ->and($seoData->og_title)->toBe('')
        ->and($seoData->og_description)->toBe('')
        ->and($seoData->og_image)->toBe('')
        ->and($seoData->twitter_image)->toBeNull()
        ->and($seoData->structured_data)->toBeNull();
});

it('is readonly and cannot be modified after creation', function (): void {
    $seoData = new SeoData(title: 'Original Title');

    expect(fn (): string => $seoData->title = 'Modified Title')
        ->toThrow(Error::class);
});

it('handles special characters in string fields', function (): void {
    $seoData = new SeoData(
        title: 'Title with & "quotes" and <HTML>',
        description: 'Description with special chars: & < > " \''
    );

    expect($seoData->title)->toBe('Title with & "quotes" and <HTML>')
        ->and($seoData->description)->toBe('Description with special chars: & < > " \'');
});

it('handles UTF-8 characters in fields', function (): void {
    $seoData = new SeoData(
        title: '测试标题 Título Заголовок',
        description: 'Ümlaut ñ characters 日本語'
    );

    expect($seoData->title)->toBe('测试标题 Título Заголовок')
        ->and($seoData->description)->toBe('Ümlaut ñ characters 日本語');
});

it('fromArray handles extra fields gracefully', function (): void {
    $data = [
        'title' => 'Test Title',
        'extra_field' => 'Should be ignored',
        'another_field' => 123,
    ];

    $seoData = SeoData::fromArray($data);

    expect($seoData->title)->toBe('Test Title')
        ->and($seoData->description)->toBe('');
});

it('structured_data can be complex nested array', function (): void {
    $complexData = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'author' => [
            '@type' => 'Person',
            'name' => 'John Doe',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Example Publisher',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://example.com/logo.png',
            ],
        ],
    ];

    $seoData = new SeoData(structured_data: $complexData);

    expect($seoData->structured_data)->toBe($complexData)
        ->and($seoData->structured_data['author']['name'])->toBe('John Doe')
        ->and($seoData->structured_data['publisher']['logo']['url'])->toBe('https://example.com/logo.png');
});
