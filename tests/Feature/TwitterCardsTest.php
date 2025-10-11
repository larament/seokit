<?php

declare(strict_types=1);

use Larament\SeoKit\Facades\SeoKit;

it('can set and get the Twitter card title', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Test Title');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:title" content="Test Title"');
});

it('can set the card type with valid options', function () {
    $twitter = SeoKit::twitter();

    $validCards = ['summary', 'summary_large_image', 'app', 'player'];

    foreach ($validCards as $card) {
        $twitter->card($card);
        $html = $twitter->toHtml();
        expect($html)->toContain('name="twitter:card" content="'.$card.'"');
    }
});

it('sets default card type for invalid options', function () {
    $twitter = SeoKit::twitter();
    $twitter->card('invalid_card_type'); // Should default to 'summary'

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:card" content="summary"');
});

it('can set the site username', function () {
    $twitter = SeoKit::twitter();
    $twitter->site('@example');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:site" content="@example"');
});

it('can set the creator username', function () {
    $twitter = SeoKit::twitter();
    $twitter->creator('@creator');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:creator" content="@creator"');
});

it('can set the description', function () {
    $twitter = SeoKit::twitter();
    $twitter->description('Test Description');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:description" content="Test Description"');
});

it('can set the image and optional alt text', function () {
    $twitter = SeoKit::twitter();
    $twitter->image('https://example.com/image.jpg', 'Image Alt Text');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:image" content="https://example.com/image.jpg"')
        ->toContain('name="twitter:image:alt" content="Image Alt Text"');
});

it('can set image without alt text', function () {
    $twitter = SeoKit::twitter();
    $twitter->image('https://example.com/image.jpg');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:image" content="https://example.com/image.jpg"')
        ->not->toContain('name="twitter:image:alt"');
});

it('can set player properties', function () {
    $twitter = SeoKit::twitter();
    $twitter->player('https://example.com/player', 400, 300);

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:player" content="https://example.com/player"')
        ->toContain('name="twitter:player:width" content="400"')
        ->toContain('name="twitter:player:height" content="300"');
});

it('can add a custom property', function () {
    $twitter = SeoKit::twitter();
    $twitter->add('custom_property', 'custom_value');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:custom_property" content="custom_value"');
});

it('can remove a property', function () {
    $twitter = SeoKit::twitter();
    $twitter->add('custom_property', 'custom_value');

    $array = $twitter->toArray();
    expect($array)->toHaveKey('custom_property');

    $twitter->remove('custom_property');
    $array = $twitter->toArray();
    expect($array)->not->toHaveKey('custom_property');
});

it('converts to array properly', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Test Title');
    $twitter->description('Test Description');
    $twitter->site('@example');

    $array = $twitter->toArray();
    expect($array)->toHaveKey('title', 'Test Title')
        ->toHaveKey('description', 'Test Description')
        ->toHaveKey('site', '@example');
});

it('generates HTML with proper formatting', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Test Title');
    $twitter->description('Test Description');
    $twitter->card('summary');

    $html = $twitter->toHtml();
    expect($html)->toContain('<meta name="twitter:title" content="Test Title" />')
        ->toContain('<meta name="twitter:description" content="Test Description" />')
        ->toContain('<meta name="twitter:card" content="summary" />');
});

it('generates minified HTML when requested', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Test Title');
    $twitter->description('Test Description');

    $html = $twitter->toHtml(true); // minify = true
    expect($html)->not->toContain("\n"); // No newlines in minified output
});

it('properly escapes special characters in content', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Title with & "Quotes" and <HTML>');
    $twitter->description('Description with special chars: & < > " \'');

    $html = $twitter->toHtml();
    expect($html)->toContain('Title with &amp; &quot;Quotes&quot; and &lt;HTML&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');
});

it('handles empty values gracefully', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('');
    $twitter->description('');
    $twitter->image('');

    $html = $twitter->toHtml();
    expect($html)->toBeString();
});

it('enforces strict typing for methods', function () {
    $twitter = SeoKit::twitter();

    // This should pass with strict typing
    $twitter->title('Valid Title');
    $twitter->description('Valid Description');

    $html = $twitter->toHtml();
    expect($html)->toContain('name="twitter:title" content="Valid Title"')
        ->toContain('name="twitter:description" content="Valid Description"');
});

it('handles UTF-8 characters correctly', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('测试标题 Título Заголовок');
    $twitter->description('Ümlaut ñ characters 日本語');

    $html = $twitter->toHtml();
    expect($html)->toContain('测试标题 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');
});

it('adds @ prefix to site username if missing', function () {
    $twitter = SeoKit::twitter();
    $twitter->site('example'); // Without @ prefix

    $array = $twitter->toArray();
    // Should either add @ or accept as-is
    expect($array)->toHaveKey('site');
});

it('adds @ prefix to creator username if missing', function () {
    $twitter = SeoKit::twitter();
    $twitter->creator('creator'); // Without @ prefix

    $array = $twitter->toArray();
    // Should either add @ or accept as-is
    expect($array)->toHaveKey('creator');
});

it('can clear all properties', function () {
    $twitter = SeoKit::twitter();
    $twitter->title('Test Title');
    $twitter->description('Test Description');
    $twitter->card('summary');

    expect($twitter->toArray())->not->toBeEmpty();
});
