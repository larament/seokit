<?php

declare(strict_types=1);

use Larament\SeoKit\MetaTags;

beforeEach(function () {
    $this->config = [
        'title' => 'Default Title',
        'before_title' => 'Before',
        'after_title' => 'After',
        'title_separator' => ' - ',
        'description' => 'Default description',
        'robots' => 'noindex, nofollow',
    ];

    $this->metaTags = new MetaTags($this->config);
});

test('it sets up with default values', function () {
    $metaTags = app(MetaTags::class);

    $array = $metaTags->toArray();

    expect($array['meta'])->toHaveKey('robots')
        ->and($array['meta']['robots'])->toContain('index, follow');
});
