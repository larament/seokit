# Images & Storage Disks

Social platforms (Facebook, X, LinkedIn, Slack) require absolute URLs for Open Graph and Twitter Card images.

SeoKit features an intelligent image URL resolver that supports external URLs, Laravel storage disks (local, public, S3), and CDN endpoints seamlessly.

## External URLs vs Storage Paths

### 1. Absolute / External URLs

If you pass a fully qualified URL (starting with `http://` or `https://`), SeoKit uses it directly without alteration:

```php
SeoKit::image('https://cdn.example.com/assets/banner.png');
```

---

### 2. Storage Disks (e.g., `public`, `s3`, `r2`)

When storing images in Laravel's filesystem disks (such as `Storage::disk('s3')` or `Storage::disk('public')`), you specify the relative file path along with the disk name.

#### In the `Seo` Model / Database

```php
$post->seo()->updateOrCreate([], [
    'og_image' => 'posts/covers/september-release.jpg',
    'og_image_disk' => 's3',
    'twitter_image' => 'posts/covers/september-twitter.jpg',
    'twitter_image_disk' => 's3',
]);
```

#### In `SeoData` DTO

```php
use Larament\SeoKit\Data\SeoData;

return new SeoData(
    title: $post->title,
    og_image: 'uploads/banners/' . $post->banner_filename,
    og_image_disk: 'public',
);
```

SeoKit invokes `Storage::disk($disk)->url($path)` and ensures the final URL is absolute.

---

## Safety & Developer Warnings

To prevent broken images on social networks caused by forgotten disk specifications:

- **Local / Testing Environment**: If a relative path is passed without specifying a storage disk, SeoKit throws a helpful `InvalidImageUrlException::missingDisk($path)` to notify the developer immediately during testing.
- **Production Environment**: In production, SeoKit logs an error via `Log::error(...)` instead of crashing the page, ensuring zero downtime for your visitors.
