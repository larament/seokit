<?php

declare(strict_types=1);

use Larament\SeoKit\Enums\OpenGraphType;
use Larament\SeoKit\Facades\SeoKit;

it('can set and get the Open Graph type', function (): void {
    $og = SeoKit::opengraph();
    $og->type('website');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="website"');
});

it('can set type with enum', function (): void {
    $og = SeoKit::opengraph();
    $og->type(OpenGraphType::Article);

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="article"');
});

it('can set and get the Open Graph URL', function (): void {
    $og = SeoKit::opengraph();
    $og->url('https://example.com');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:url" content="https://example.com"');
});

it('can set and get the Open Graph title', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Test Title');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:title" content="Test Title"');
});

it('can set and get the Open Graph description', function (): void {
    $og = SeoKit::opengraph();
    $og->description('Test Description');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:description" content="Test Description"');
});

it('can set site name', function (): void {
    $og = SeoKit::opengraph();
    $og->siteName('Test Site');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:site_name" content="Test Site"');
});

it('can set locale', function (): void {
    $og = SeoKit::opengraph();
    $og->locale('en_US');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:locale" content="en_US"');
});

it('can add locale alternate', function (): void {
    $og = SeoKit::opengraph();
    $og->localeAlternate(['es_ES', 'fr_FR']);

    $html = $og->toHtml();
    expect($html)->toContain('property="og:locale:alternate" content="es_ES"')
        ->toContain('property="og:locale:alternate" content="fr_FR"');
});

it('can conditionally add locale alternate', function (): void {
    $og = SeoKit::opengraph();
    $og->localeAlternate([]); // Empty array should not add anything

    $html = $og->toHtml();
    expect($html)->not->toContain('property="og:locale:alternate"');
});

it('can set determiner', function (): void {
    $og = SeoKit::opengraph();

    // Valid determiners
    $validDeterminers = ['a', 'an', 'the', '', 'auto'];
    foreach ($validDeterminers as $determiner) {
        $og->determiner($determiner);
        $html = $og->toHtml();
        if ($determiner !== '') {
            expect($html)->toContain('property="og:determiner" content="'.$determiner.'"');
        } else {
            expect($html)->toContain('property="og:determiner" content=""');
        }
    }
});

it('does not set invalid determiner', function (): void {
    $og = SeoKit::opengraph();
    $og->determiner('invalid'); // This should be ignored

    $html = $og->toHtml();
    expect($html)->not->toContain('property="og:determiner"');
});

it('can add an image with all properties', function (): void {
    $og = SeoKit::opengraph();
    $og->image(
        'https://example.com/image.jpg',
        'https://secure.example.com/image.jpg',
        'image/jpeg',
        800,
        600,
        'Image Alt Text'
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:image" content="https://example.com/image.jpg"')
        ->toContain('property="og:image:secure_url" content="https://secure.example.com/image.jpg"')
        ->toContain('property="og:image:type" content="image/jpeg"')
        ->toContain('property="og:image:width" content="800"')
        ->toContain('property="og:image:height" content="600"')
        ->toContain('property="og:image:alt" content="Image Alt Text"');
});

it('can add a video with all properties', function (): void {
    $og = SeoKit::opengraph();
    $og->video(
        'https://example.com/video.mp4',
        'https://secure.example.com/video.mp4',
        'video/mp4',
        1280,
        720
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:video" content="https://example.com/video.mp4"')
        ->toContain('property="og:video:secure_url" content="https://secure.example.com/video.mp4"')
        ->toContain('property="og:video:type" content="video/mp4"')
        ->toContain('property="og:video:width" content="1280"')
        ->toContain('property="og:video:height" content="720"');
});

it('can add an audio with all properties', function (): void {
    $og = SeoKit::opengraph();
    $og->audio(
        'https://example.com/audio.mp3',
        'https://secure.example.com/audio.mp3',
        'audio/mpeg'
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:audio" content="https://example.com/audio.mp3"')
        ->toContain('property="og:audio:secure_url" content="https://secure.example.com/audio.mp3"')
        ->toContain('property="og:audio:type" content="audio/mpeg"');
});

it('can set article properties', function (): void {
    $og = SeoKit::opengraph();
    $og->article(
        '2023-01-01T00:00:00Z',
        '2023-01-02T00:00:00Z',
        null,
        ['Author Name'],
        'Technology',
        ['tag1', 'tag2']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="article"')
        ->toContain('property="article:published_time" content="2023-01-01T00:00:00Z"')
        ->toContain('property="article:modified_time" content="2023-01-02T00:00:00Z"')
        ->toContain('property="article:section" content="Technology"')
        ->toContain('property="article:author" content="Author Name"')
        ->toContain('property="article:tag" content="tag1"')
        ->toContain('property="article:tag" content="tag2"');
});

it('can set profile properties', function (): void {
    $og = SeoKit::opengraph();
    $og->profile('John', 'Doe', 'johndoe', 'male');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="profile"')
        ->toContain('property="profile:first_name" content="John"')
        ->toContain('property="profile:last_name" content="Doe"')
        ->toContain('property="profile:username" content="johndoe"')
        ->toContain('property="profile:gender" content="male"');
});

it('can set book properties', function (): void {
    $og = SeoKit::opengraph();
    $og->book(['Author 1', 'Author 2'], '123456789', '2023-01-01', ['fiction', 'adventure']);

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="book"')
        ->toContain('property="book:author" content="Author 1"')
        ->toContain('property="book:isbn" content="123456789"')
        ->toContain('property="book:release_date" content="2023-01-01"')
        ->toContain('property="book:tag" content="fiction"')
        ->toContain('property="book:tag" content="adventure"');
});

it('can add a custom property', function (): void {
    $og = SeoKit::opengraph();
    $og->add('custom:property', 'custom value');

    $html = $og->toHtml();
    expect($html)->toContain('property="custom:property" content="custom value"');
});

it('can conditionally add a custom property', function (): void {
    $og = SeoKit::opengraph();
    $og->addWhen(true, 'custom:property', 'custom value');

    $html = $og->toHtml();
    expect($html)->toContain('property="custom:property" content="custom value"');
});

it('does not add property when condition is false', function (): void {
    $og = SeoKit::opengraph();
    $og->addWhen(false, 'custom:property', 'custom value');

    $html = $og->toHtml();
    expect($html)->not->toContain('property="custom:property"');
});

it('can remove a property', function (): void {
    $og = SeoKit::opengraph();
    $og->add('custom:property', 'custom value');
    expect($og->has('custom:property'))->toBeTrue();

    $og->remove('custom:property');
    expect($og->has('custom:property'))->toBeFalse();
});

it('can get a property value', function (): void {
    $og = SeoKit::opengraph();
    $og->add('custom:property', 'custom value');

    expect($og->get('custom:property'))->toBe('custom value');
});

it('returns null for non-existent property', function (): void {
    $og = SeoKit::opengraph();

    expect($og->get('nonexistent:property'))->toBeNull();
});

it('converts to array properly', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Test Title');
    $og->add('custom:property', 'custom value');

    $array = $og->toArray();
    expect($array)->toHaveKey('og:title', 'Test Title')
        ->toHaveKey('custom:property', 'custom value');
});

it('can clear all properties', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Test Title');
    $og->add('custom:property', 'custom value');

    expect($og->toArray())->not->toBeEmpty();

    $og->clear();
    expect($og->toArray())->toBeEmpty();
});

it('generates HTML with proper formatting', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Test Title');
    $og->description('Test Description');
    $og->type('website');

    $html = $og->toHtml();
    expect($html)->toContain('<meta property="og:title" content="Test Title" />')
        ->toContain('<meta property="og:description" content="Test Description" />')
        ->toContain('<meta property="og:type" content="website" />');
});

it('generates minified HTML when requested', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Test Title');
    $og->description('Test Description');

    $html = $og->toHtml(true); // minify = true
    expect($html)->not->toContain("\n"); // No newlines in minified output
});

it('properly escapes special characters in content', function (): void {
    $og = SeoKit::opengraph();
    $og->title('Title with & "Quotes" and <HTML>');
    $og->description('Description with special chars: & < > " \'');

    $html = $og->toHtml();
    expect($html)->toContain('Title with &amp; &quot;Quotes&quot; and &lt;HTML&gt;')
        ->toContain('Description with special chars: &amp; &lt; &gt; &quot; &#039;');
});

it('handles empty values gracefully', function (): void {
    $og = SeoKit::opengraph();
    $og->title('');
    $og->description('');
    $og->url('');

    $html = $og->toHtml();
    expect($html)->toBeString();
});

it('enforces strict typing for methods', function (): void {
    $og = SeoKit::opengraph();

    // This should pass with strict typing
    $og->title('Valid Title');
    $og->description('Valid Description');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:title" content="Valid Title"')
        ->toContain('property="og:description" content="Valid Description"');
});

it('handles multiple images correctly', function (): void {
    $og = SeoKit::opengraph();
    $og->image('https://example.com/image1.jpg');
    $og->add('og:image', 'https://example.com/image2.jpg');

    $html = $og->toHtml();
    expect($html)->toContain('https://example.com/image1.jpg');
});

it('validates URLs in url property', function (): void {
    $og = SeoKit::opengraph();
    $og->url('https://example.com/page');

    $html = $og->toHtml();
    expect($html)->toContain('property="og:url" content="https://example.com/page"');
});

it('can set multiple authors for article', function (): void {
    $og = SeoKit::opengraph();
    $og->article(
        '2023-01-01T00:00:00Z',
        null,
        null,
        ['Author 1', 'Author 2', 'Author 3'],
        'Technology',
        []
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="article:author" content="Author 1"')
        ->toContain('property="article:author" content="Author 2"')
        ->toContain('property="article:author" content="Author 3"');
});

it('handles UTF-8 characters correctly', function (): void {
    $og = SeoKit::opengraph();
    $og->title('测试标题 Título Заголовок');
    $og->description('Ümlaut ñ characters 日本語');

    $html = $og->toHtml();
    expect($html)->toContain('测试标题 Título Заголовок')
        ->toContain('Ümlaut ñ characters 日本語');
});

it('can set music song properties', function (): void {
    $og = SeoKit::opengraph();
    $og->musicSong(
        duration: 240,
        album: ['https://example.com/album1'],
        albumDisc: 1,
        albumTrack: 3,
        musician: ['https://example.com/artist']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="music.song"')
        ->toContain('property="music:duration" content="240"')
        ->toContain('property="music:album:disc" content="1"')
        ->toContain('property="music:album:track" content="3"');
});

it('can set music album properties', function (): void {
    $og = SeoKit::opengraph();
    $og->musicAlbum(
        song: ['https://example.com/song1', 'https://example.com/song2'],
        musician: ['https://example.com/artist'],
        releaseDate: '2023-01-01'
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="music.album"')
        ->toContain('property="music:release_date" content="2023-01-01"');
});

it('can set music playlist properties', function (): void {
    $og = SeoKit::opengraph();
    $og->musicPlaylist(
        song: ['https://example.com/song1'],
        creator: ['https://example.com/creator']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="music.playlist"');
});

it('can set music radio station properties', function (): void {
    $og = SeoKit::opengraph();
    $og->musicRadioStation(
        creator: ['https://example.com/station']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="music.radio_station"');
});

it('can set video movie properties', function (): void {
    $og = SeoKit::opengraph();
    $og->videoMovie(
        actor: ['https://example.com/actor1'],
        actorRole: ['Lead Role'],
        director: ['https://example.com/director'],
        writer: ['https://example.com/writer'],
        duration: 7200,
        releaseDate: '2023-01-01',
        tag: ['action', 'thriller']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="video.movie"')
        ->toContain('property="video:duration" content="7200"')
        ->toContain('property="video:release_date" content="2023-01-01"');
});

it('can set video episode properties', function (): void {
    $og = SeoKit::opengraph();
    $og->videoEpisode(
        series: 'https://example.com/series',
        actor: ['https://example.com/actor'],
        director: ['https://example.com/director'],
        duration: 2700
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="video.episode"')
        ->toContain('property="video:series" content="https://example.com/series"')
        ->toContain('property="video:duration" content="2700"');
});

it('can set video tv show properties', function (): void {
    $og = SeoKit::opengraph();
    $og->videoTvShow(
        actor: ['https://example.com/actor'],
        director: ['https://example.com/director'],
        tag: ['drama', 'comedy']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="video.tv_show"');
});

it('can set video other properties', function (): void {
    $og = SeoKit::opengraph();
    $og->videoOther(
        actor: ['https://example.com/actor'],
        duration: 300,
        tag: ['educational']
    );

    $html = $og->toHtml();
    expect($html)->toContain('property="og:type" content="video.other"')
        ->toContain('property="video:duration" content="300"');
});
