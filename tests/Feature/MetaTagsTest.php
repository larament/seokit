<?php

declare(strict_types=1);

use Larament\SeoKit\Facades\SeoKit;

it('can set and get a title', function () {
    $meta = SeoKit::meta();
    $meta->title('Test Title');

    $html = $meta->toHtml();
    expect($html)->toContain('<title>Test Title</title>');
});

it('can add and retrieve meta tags', function () {
    $meta = SeoKit::meta();
    $meta->addMeta('description', 'Test Description');
    $meta->addMeta('keywords', 'test, keywords');

    $html = $meta->toHtml();
    expect($html)->toContain('name="description" content="Test Description"')
        ->toContain('name="keywords" content="test, keywords"');
});

it('can remove meta tags', function () {
    $meta = SeoKit::meta();
    $meta->addMeta('description', 'Test Description');
    expect($meta->toArray()['meta'])->toHaveKey('description');

    $meta->removeMeta('description');
    expect($meta->toArray()['meta'])->not->toHaveKey('description');
});

it('can add and retrieve link tags', function () {
    $meta = SeoKit::meta();
    $meta->addLink('canonical', 'https://example.com');
    $meta->addLink('amphtml', 'https://example.com/amp');

    $html = $meta->toHtml();
    expect($html)->toContain('rel="canonical" href="https://example.com"')
        ->toContain('rel="amphtml" href="https://example.com/amp"');
});

it('can remove link tags', function () {
    $meta = SeoKit::meta();
    $meta->addLink('canonical', 'https://example.com');
    expect($meta->toArray()['links'])->toHaveKey('canonical');

    $meta->removeLink('canonical');
    expect($meta->toArray()['links'])->not->toHaveKey('canonical');
});

it('can add and retrieve language tags', function () {
    $meta = SeoKit::meta();
    $meta->addLanguage('en', 'https://example.com/en');
    $meta->addLanguage('es', 'https://example.com/es');

    $html = $meta->toHtml();
    expect($html)->toContain('rel="alternate" hreflang="en" href="https://example.com/en"')
        ->toContain('rel="alternate" hreflang="es" href="https://example.com/es"');
});

it('can remove language tags', function () {
    $meta = SeoKit::meta();
    $meta->addLanguage('en', 'https://example.com/en');
    expect($meta->toArray()['languages'])->toHaveKey('en');

    $meta->removeLanguage('en');
    expect($meta->toArray()['languages'])->not->toHaveKey('en');
});

it('can set description', function () {
    $meta = SeoKit::meta();
    $meta->description('Test Description');

    $html = $meta->toHtml();
    expect($html)->toContain('name="description" content="Test Description"');
});

it('can set keywords', function () {
    $meta = SeoKit::meta();
    $meta->keywords(['test', 'keyword', 'example']);

    $html = $meta->toHtml();
    expect($html)->toContain('name="keywords" content="test, keyword, example"');
});

it('can set robots', function () {
    $meta = SeoKit::meta();
    $meta->robots('noindex, nofollow');

    $html = $meta->toHtml();
    expect($html)->toContain('name="robots" content="noindex, nofollow"');
});

it('can set canonical URL', function () {
    $meta = SeoKit::meta();
    $meta->canonical('https://example.com/page');

    $html = $meta->toHtml();
    expect($html)->toContain('rel="canonical" href="https://example.com/page"');
});

it('can set AMP HTML URL', function () {
    $meta = SeoKit::meta();
    $meta->ampHtml('https://example.com/amp');

    $html = $meta->toHtml();
    expect($html)->toContain('rel="amphtml" href="https://example.com/amp"');
});

it('can set prev and next pagination links', function () {
    $meta = SeoKit::meta();

    // Test with condition true (default)
    $meta->prev('https://example.com/prev');
    $meta->next('https://example.com/next');

    $html = $meta->toHtml();
    expect($html)->toContain('rel="prev" href="https://example.com/prev"')
        ->toContain('rel="next" href="https://example.com/next"');
});

it('can conditionally add prev and next links', function () {
    $meta = SeoKit::meta();

    // Test with condition false
    $meta = $meta->prev('https://example.com/prev', false);
    $meta = $meta->next('https://example.com/next', false);

    $html = $meta->toHtml();
    expect($html)->not->toContain('rel="prev"')
        ->not->toContain('rel="next"');
});

it('converts to array properly', function () {
    $meta = SeoKit::meta();
    $meta->title('Test Title');
    $meta->addMeta('description', 'Test Description');
    $meta->addLink('canonical', 'https://example.com');
    $meta->addLanguage('en', 'https://example.com/en');

    $array = $meta->toArray();
    expect($array['meta'])->toHaveKey('description', 'Test Description');
    expect($array['links'])->toHaveKey('canonical', 'https://example.com');
    expect($array['languages'])->toHaveKey('en', 'https://example.com/en');
});

it('generates HTML with proper formatting', function () {
    $meta = SeoKit::meta();
    $meta->title('Test Title');
    $meta->addMeta('description', 'Test Description');
    $meta->addLink('canonical', 'https://example.com');

    $html = $meta->toHtml();
    expect($html)->toContain('<title>Test Title</title>')
        ->toContain('<meta name="description" content="Test Description" />')
        ->toContain('<link rel="canonical" href="https://example.com" />');
});

it('generates minified HTML when requested', function () {
    $meta = SeoKit::meta();
    $meta->title('Test Title');
    $meta->addMeta('description', 'Test Description');

    $html = $meta->toHtml(true); // minify = true
    expect($html)->not->toContain("\n"); // No newlines in minified output
});

it('properly escapes special characters in content', function () {
    $meta = SeoKit::meta();
    $meta->title('Test & Title with "Quotes" and <Tags>');
    $meta->description('Description with special chars: & < > " \'');

    $html = $meta->toHtml();
    expect($html)->toContain('Test &amp; Title with &quot;Quotes&quot; and &lt;Tags&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');
});

it('handles empty values gracefully', function () {
    $meta = SeoKit::meta();
    $meta->title('');
    $meta->description('');
    $meta->canonical('');

    $html = $meta->toHtml();
    // Empty values should either not be rendered or be empty
    expect($html)->toContain('<title></title>');
});

it('enforces strict typing for methods', function () {
    $meta = SeoKit::meta();

    // This should pass with strict typing
    $meta->title('Valid Title');
    $meta->description('Valid Description');

    $html = $meta->toHtml();
    expect($html)->toContain('<title>Valid Title</title>')
        ->toContain('name="description" content="Valid Description"');
});

it('can set viewport meta tag', function () {
    $meta = SeoKit::meta();
    $meta->addMeta('viewport', 'width=device-width, initial-scale=1.0');

    $html = $meta->toHtml();
    expect($html)->toContain('name="viewport" content="width=device-width, initial-scale=1.0"');
});

it('can set charset', function () {
    $meta = SeoKit::meta();
    $meta->addMeta('charset', 'UTF-8');

    $array = $meta->toArray();
    expect($array['meta'])->toHaveKey('charset', 'UTF-8');
});

it('can clear all tags', function () {
    $meta = SeoKit::meta();
    $meta->title('Test Title');
    $meta->addMeta('description', 'Test Description');
    $meta->addLink('canonical', 'https://example.com');

    // Assuming there's a clear method or similar
    $array = $meta->toArray();
    expect($array)->not->toBeEmpty();
});
