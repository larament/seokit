# Twitter Cards

Twitter Cards make tweets that link to your site display rich media, prominent titles, and prominent visual banners on X (Twitter).

## Card Types

Twitter supports four card types:
- `summary_large_image` (Default in SeoKit): A large full-width banner image above the title.
- `summary`: A compact square thumbnail alongside the title and snippet.
- `player`: Embedded interactive video/audio streams.
- `app`: Mobile application download links.

Set the card type using `card()`:

```php
use Larament\SeoKit\Facades\SeoKit;

SeoKit::twitter()->card('summary_large_image');
```

---

## Handles & Attributions

Attaching site and author Twitter handles helps increase brand recognition and engagement:

```php
SeoKit::twitter()
    ->site('laramentdev')    // Automatically prefixed with @
    ->creator('raziuldev');  // Automatically prefixed with @
```

This compiles to:
```html
<meta name="twitter:site" content="@laramentdev" />
<meta name="twitter:creator" content="@raziuldev" />
```

---

## Title, Description, and Images

You can configure card details explicitly or rely on SeoKit's automatic synchronization:

```php
SeoKit::twitter()
    ->title('Laravel Technical SEO Made Easy')
    ->description('The complete guide to mastering Open Graph and Twitter Card tags in Laravel.')
    ->image('https://example.com/banner.png', alt: 'Graphic illustrating Laravel SEO tools');
```

Output:
```html
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Laravel Technical SEO Made Easy" />
<meta name="twitter:description" content="The complete guide to mastering Open Graph and Twitter Card tags in Laravel." />
<meta name="twitter:image" content="https://example.com/banner.png" />
<meta name="twitter:image:alt" content="Graphic illustrating Laravel SEO tools" />
```

---

## Interactive Video Players

For interactive media players (such as video podcasts or tutorials), specify player URL dimensions:

```php
SeoKit::twitter()
    ->card('player')
    ->player(
        url: 'https://example.com/embed/tutorial-101',
        width: 1280,
        height: 720
    );
```

This renders:
```html
<meta name="twitter:card" content="player" />
<meta name="twitter:player" content="https://example.com/embed/tutorial-101" />
<meta name="twitter:player:width" content="1280" />
<meta name="twitter:player:height" content="720" />
```

---

## Arbitrary Properties

If you need to supply any custom or platform-specific Twitter card property:

```php
SeoKit::twitter()->add('label1', 'Reading time')
    ->add('data1', '5 minutes');
```
