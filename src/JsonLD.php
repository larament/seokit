<?php

declare(strict_types=1);

namespace Larament\SeoKit;

final class JsonLD
{
    private array $schemas = [];

    /**
     * Add a JSON-LD schema to the collection.
     */
    public function add(array $schema): self
    {
        $this->schemas[] = $schema;

        return $this;
    }

    /**
     * Remove a JSON-LD schema from the collection by index.
     */
    public function remove(int $index): self
    {
        if (isset($this->schemas[$index])) {
            unset($this->schemas[$index]);
        }

        return $this;
    }

    /**
     * Add a WebSite schema.
     */
    public function website(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => request()->url(),
            'name' => config('app.name'),
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add an Organization schema.
     */
    public function organization(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add a Person schema.
     */
    public function person(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => config('app.name'),
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add an Article schema.
     */
    public function article(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => '',
            'description' => '',
            'author' => [
                '@type' => 'Person',
                'name' => config('app.name'),
            ],
            'datePublished' => now()->toISOString(),
            'dateModified' => now()->toISOString(),
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add a BlogPosting schema.
     */
    public function blogPosting(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => '',
            'description' => '',
            'author' => [
                '@type' => 'Person',
                'name' => config('app.name'),
            ],
            'datePublished' => now()->toISOString(),
            'dateModified' => now()->toISOString(),
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add a Product schema.
     */
    public function product(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => '',
            'description' => '',
            'offers' => [
                '@type' => 'Offer',
                'price' => '',
                'priceCurrency' => 'USD',
            ],
        ], $data);

        return $this->add($schema);
    }

    /**
     * Add a LocalBusiness schema.
     */
    public function localBusiness(array $data = []): self
    {
        $schema = array_merge([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => config('app.name'),
            'description' => '',
            'address' => '',
            'telephone' => '',
            'openingHours' => '',
        ], $data);

        return $this->add($schema);
    }

    /**
     * Clear all schemas.
     */
    public function clear(): self
    {
        $this->schemas = [];

        return $this;
    }

    /**
     * Get all schemas as an array.
     */
    public function toArray(): array
    {
        return $this->schemas;
    }

    /**
     * Render the JSON-LD schemas to HTML script tags.
     */
    public function toHtml(bool $minify = false): string
    {
        if (empty($this->schemas)) {
            return '';
        }

        $output = [];

        foreach ($this->schemas as $schema) {
            $output[] = sprintf(
                '<script type="application/ld+json">%s</script>',
                json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            );
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }
}
