# Open Graph

Open Graph meta tags power rich previews when your links are shared across platforms like Facebook, LinkedIn, Slack, WhatsApp, and Discord.

SeoKit supports the full Open Graph specification, from basic cards to rich media, articles, books, and profiles.

## Basic Open Graph Setup

You can configure Open Graph via the `SeoKit` facade shortcuts or the dedicated `opengraph()` manager:

```php
use Larament\SeoKit\Facades\SeoKit;

SeoKit::opengraph()
    ->title('Laravel Performance Optimization Guide')
    ->description('In-depth techniques for caching, database queries, and queue scaling.')
    ->url('https://example.com/blog/performance-guide')
    ->siteName('My Application')
    ->type('article')
    ->locale('en_US');
```

::: tip Synchronization
When you use top-level shortcuts like `SeoKit::title()` or `SeoKit::description()`, SeoKit automatically synchronizes them to Open Graph tags unless you override them specifically on `SeoKit::opengraph()`.
:::

---

## Adding Images

SeoKit allows you to define images with dimensions, MIME types, secure URLs, and accessibility alt text:

```php
SeoKit::opengraph()->image(
    url: 'https://example.com/images/cover.jpg',
    secureUrl: 'https://example.com/images/cover.jpg',
    type: 'image/jpeg',
    width: 1200,
    height: 630,
    alt: 'Cover graphic showing Laravel code snippet'
);
```

This renders:
```html
<meta property="og:image" content="https://example.com/images/cover.jpg">
<meta property="og:image:secure_url" content="https://example.com/images/cover.jpg">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Cover graphic showing Laravel code snippet">
```

Multiple images can be added simply by calling `image()` multiple times.

---

## Rich Media (Video & Audio)

Add video or audio attachments for media streaming or podcast episodes:

```php
// Add Video
SeoKit::opengraph()->video(
    url: 'https://example.com/videos/tutorial.mp4',
    secureUrl: 'https://example.com/videos/tutorial.mp4',
    type: 'video/mp4',
    width: 1920,
    height: 1080
);

// Add Audio
SeoKit::opengraph()->audio(
    url: 'https://example.com/podcasts/episode-12.mp3',
    secureUrl: 'https://example.com/podcasts/episode-12.mp3',
    type: 'audio/mpeg'
);
```

---

## Specialized Content Types

### Article Metadata

When sharing blog posts or news items, use `article()` to attach publication dates, author profiles, sections, and tags:

```php
SeoKit::opengraph()->article(
    publishedTime: '2026-03-15T08:00:00Z',
    modifiedTime: '2026-03-16T10:30:00Z',
    authors: ['https://example.com/authors/raziul'],
    section: 'Engineering',
    tags: ['laravel', 'php', 'architecture']
);
```

### Profile Metadata

For user profiles or author pages:

```php
SeoKit::opengraph()->profile(
    firstName: 'Raziul',
    lastName: 'Islam',
    username: 'raziuldev',
    gender: 'male'
);
```

### Book & Music Types

SeoKit also provides specialized helpers for:
- `book(array $author, ?string $isbn, ?string $releaseDate, array $tags)`
- `musicSong(...)`, `musicAlbum(...)`, `musicPlaylist(...)`, and `musicRadioStation(...)`

---

## Conditional Properties

Use `addWhen()` to append Open Graph properties conditionally:

```php
SeoKit::opengraph()->addWhen(
    $post->is_premium,
    'custom:subscription_tier',
    'pro'
);
```
