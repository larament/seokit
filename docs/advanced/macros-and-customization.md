# Macros & Extensions

The core `Larament\SeoKit\SeoKitManager` incorporates Laravel's `Macroable` trait. This enables you to define domain-specific shortcuts, company-wide defaults, or integration helpers without modifying package internals.

## Defining a Macro

Register your macros in the `boot()` method of any Service Provider (for instance, `app/Providers/AppServiceProvider.php`):

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Larament\SeoKit\Facades\SeoKit;
use Larament\SeoKit\SeoKitManager;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Define a macro for standard blog posts
        SeoKitManager::macro('forBlogPost', function ($post) {
            /** @var SeoKitManager $this */
            $this->title($post->title)
                ->description($post->excerpt)
                ->image($post->featured_image)
                ->canonical(route('posts.show', $post));

            $this->opengraph()->article(
                publishedTime: $post->published_at?->toISOString(),
                authors: [$post->author->name],
                section: $post->category->name
            );

            return $this;
        });
    }
}
```

---

## Calling Your Macro

Once defined, call your macro directly on the `SeoKit` facade:

```php
use Larament\SeoKit\Facades\SeoKit;

public function show(Post $post)
{
    SeoKit::forBlogPost($post);

    return view('posts.show', compact('post'));
}
```

---

## Extending Meta or OpenGraph Managers

Because `SeoKitManager` provides direct access to its underlying managers (`meta()`, `opengraph()`, `twitter()`, and `jsonld()`), macros can combine multiple layers seamlessly to fit any custom design or enterprise requirement.
