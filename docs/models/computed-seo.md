# Computed SEO Data

If your models already contain standard fields (such as `title`, `excerpt`, and `featured_image`), you may not need a separate database table for SEO.

The `HasSeoData` trait allows your model to compute its SEO metadata on-the-fly.

## When to Use Computed SEO

Use **Computed SEO** (`HasSeoData`) when:
- You don't want an extra database table or migration.
- SEO titles and descriptions are derived directly from existing model attributes.
- You have simple models where separate social/meta overrides are not needed.

Use **Database-Backed SEO** (`HasSeo`) when:
- Content managers need custom meta titles, custom Open Graph images, or robots overrides distinct from the model's displayed content.

---

## Implementing `HasSeoData`

Add `Larament\SeoKit\Concerns\HasSeoData` to your model and implement the `toSeoData()` method:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Larament\SeoKit\Concerns\HasSeoData;
use Larament\SeoKit\Data\SeoData;

class Product extends Model
{
    use HasSeoData;

    public function toSeoData(): SeoData
    {
        return new SeoData(
            title: $this->name,
            description: $this->short_description,
            canonical: route('products.show', $this),
            og_title: $this->name,
            og_description: $this->short_description,
            og_image: $this->main_image_path,
            og_image_disk: 'public',
            twitter_image: $this->main_image_path,
            twitter_image_disk: 'public',
            structured_data: [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $this->name,
                'description' => $this->short_description,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => (string) $this->price,
                    'priceCurrency' => 'USD',
                ],
            ],
        );
    }
}
```

---

## Applying in Controllers

Just as with database-backed models, call `prepareSeoTags()`:

```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->prepareSeoTags();

        return view('products.show', compact('product'));
    }
}
```

`prepareSeoTags()` runs `toSeoData()` and applies all attributes into `SeoKit::fromSeoData()`.
