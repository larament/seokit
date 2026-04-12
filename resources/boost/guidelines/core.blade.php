## SeoKit

Laravel package for managing technical SEO metadata (meta tags, Open Graph, Twitter cards, JSON-LD) from a single API.

### Facade API

- `SeoKit::title($title)` - Set title for all channels
- `SeoKit::description($description)` - Set description for all channels
- `SeoKit::image($image)` - Set image for Open Graph and Twitter
- `SeoKit::canonical($url)` - Set canonical URL
- `SeoKit::meta()->keywords(['keyword1', 'keyword2'])` - Set meta keywords
- `SeoKit::meta()->robots('index, follow')` - Set robots directive
- `SeoKit::opengraph()->...` - Open Graph specific methods
- `SeoKit::twitter()->...` - Twitter card specific methods
- `SeoKit::jsonld()->add($data)` - Add JSON-LD structured data

### Rendering

Blade directive `@seoKit` renders all SEO tags inside `<head>`. Use `@seoKit(true)` for minified output.

### SeoData DTO

Use `Larament\SeoKit\Data\SeoData` as the canonical DTO for model SEO metadata. Available fields:
- `title`, `description`, `keywords`, `canonical`, `robots`
- `og_title`, `og_description`, `og_image`
- `twitter_image`, `structured_data`

### Model Integration

- `HasSeo` trait - Database-backed SEO via polymorphic `seo()` relationship
- `HasSeoData` trait - Derive SEO directly from model attributes via `toSeoData(): SeoData`
- Use `fallbackSeoData()` in models for optional fallback values
- Non-empty relation data takes precedence over fallback

### Commands

@verbatim
<code-snippet name="Install SeoKit" lang="shell">
php artisan seokit:install
</code-snippet>
@endverbatim

### Best Practices

- Keep fallback logic explicit and model-specific
- Add tests when SEO merge/fallback behavior changes
- Preserve `SeoData` field names to avoid breaking consumers
