<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Traits\Macroable;
use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Support\Util;

final class SeoKitManager
{
    use Macroable;

    public function __construct(
        private MetaTags $meta,
        private OpenGraph $opengraph,
        private TwitterCards $twitter,
        private JsonLD $jsonld,
    ) {
        $this->setDefaultMetaTags();
        $this->setDefaultOpenGraph();
        $this->setDefaultTwitter();
        $this->setDefaultJsonLd();
    }

    public function meta(): MetaTags
    {
        return $this->meta;
    }

    public function opengraph(): OpenGraph
    {
        return $this->opengraph;
    }

    public function twitter(): TwitterCards
    {
        return $this->twitter;
    }

    public function jsonld(): JsonLD
    {
        return $this->jsonld;
    }

    /**
     * Set the SEO data for the manager from a SeoData object.
     */
    public function fromSeoData(SeoData $data): static
    {
        $this->meta
            ->title($data->title)
            ->description($data->description);

        if ($data->robots) {
            $this->meta->robots($data->robots);
        }
        if ($data->canonical) {
            $this->meta->canonical($data->canonical);
        }

        $this->opengraph
            ->title($data->og_title ?: $data->title)
            ->description($data->og_description ?: $data->description);
        if ($data->og_image) {
            $this->opengraph->image($data->og_image);
        }

        $this->twitter
            ->title($data->og_title ?: $data->title)
            ->description($data->og_description ?: $data->description);
        if ($twImage = $data->twitter_image ?? $data->og_image) {
            $this->twitter->image($twImage);
        }

        if ($data->structured_data) {
            $this->jsonld->add($data->structured_data);
        }

        return $this;
    }

    /**
     * Set the title for all the meta tags.
     */
    public function title(string $title): static
    {
        $this->meta->title($title);
        $this->opengraph->title($title);
        $this->twitter->title($title);

        return $this;
    }

    /**
     * Set the description for all the meta tags.
     */
    public function description(string $description): static
    {
        $this->meta->description($description);
        $this->opengraph->description($description);
        $this->twitter->description($description);

        return $this;
    }

    /**
     * Set the image for Open Graph and Twitter.
     */
    public function image(string $image): static
    {
        $this->opengraph->image($image);
        $this->twitter->image($image);

        return $this;
    }

    /**
     * Set the canonical URL for the meta tags and Open Graph.
     */
    public function canonical(string $canonical): static
    {
        $this->meta->canonical($canonical);
        $this->opengraph->url($canonical);

        return $this;
    }

    /**
     * Render the SEO tags to HTML.
     */
    public function toHtml(bool $minify = false): string
    {
        $output = [
            $this->meta->toHtml($minify),
        ];

        if (config('seokit.opengraph.enabled')) {
            $output[] = $this->opengraph->toHtml($minify);
        }

        if (config('seokit.twitter.enabled')) {
            $output[] = $this->twitter->toHtml($minify);
        }

        if (config('seokit.json_ld.enabled')) {
            $output[] = $this->jsonld->toHtml($minify);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }

    /**
     * Set the default meta tags.
     */
    private function setDefaultMetaTags(): void
    {
        $meta = config('seokit.defaults');
        $this->meta->title(config('seokit.auto_title_from_url') ? Util::getTitleFromUrl() : $meta['title']);

        if ($meta['description']) {
            $this->meta->description($meta['description']);
        }

        if ($meta['robots']) {
            $this->meta->robots($meta['robots']);
        }

        match ($meta['canonical'] ?? null) {
            null => $this->meta->canonical(URL::current()),
            'full' => $this->meta->canonical(URL::full()),
            default => null,
        };

    }

    /**
     * Set the default open graph.
     */
    private function setDefaultOpenGraph(): void
    {
        $opengraph = config('seokit.opengraph.defaults');
        $this->opengraph
            ->type($opengraph['type'])
            ->siteName($opengraph['site_name'])
            ->locale($opengraph['locale']);

        match ($opengraph['url'] ?? null) {
            null => $this->opengraph->url(URL::current()),
            'full' => $this->opengraph->url(URL::full()),
            default => null,
        };

    }

    /**
     * Set the default twitter.
     */
    private function setDefaultTwitter(): void
    {
        $twitter = config('seokit.twitter.defaults');
        if ($twitter['card']) {
            $this->twitter->card($twitter['card']);
        }
        if ($twitter['site']) {
            $this->twitter->site($twitter['site']);
        }
        if ($twitter['creator']) {
            $this->twitter->creator($twitter['creator']);
        }

    }

    /**
     * Set the default json ld.
     */
    private function setDefaultJsonLd(): void
    {
        $jsonld = config('seokit.json_ld.defaults');
        if ($jsonld) {
            $this->jsonld->add($jsonld);
        }
    }
}
