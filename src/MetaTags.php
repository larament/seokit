<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Larament\SeoKit\Support\Util;

final class MetaTags
{
    private ?string $title = null;

    private array $meta = [];

    private array $links = [];

    private array $languages = [];

    /**
     * Add a meta tag to the page.
     */
    public function addMeta(string $name, string $content): self
    {
        $this->meta[$name] = e($content);

        return $this;
    }

    /**
     * Remove a meta tag by name.
     */
    public function removeMeta(string $name): self
    {
        unset($this->meta[$name]);

        return $this;
    }

    /**
     * Add a link tag to the page.
     */
    public function addLink(string $rel, string $href): self
    {
        $this->links[$rel] = e($href);

        return $this;
    }

    /**
     * Remove a link tag by rel attribute.
     */
    public function removeLink(string $rel): self
    {
        unset($this->links[$rel]);

        return $this;
    }

    /**
     * Add an alternate language link tag.
     */
    public function addLanguage(string $hreflang, string $href): self
    {
        $this->languages[$hreflang] = Util::cleanString($href);

        return $this;
    }

    /**
     * Remove an alternate language link tag.
     */
    public function removeLanguage(string $hreflang): self
    {
        unset($this->languages[$hreflang]);

        return $this;
    }

    /**
     * Set the page title.
     */
    public function title(string $title): self
    {
        $this->title = Util::affixTitle(
            Util::cleanString($title)
        );

        return $this;
    }

    /**
     * Set the meta description.
     */
    public function description(string $description): self
    {
        return $this->addMeta('description', $description);
    }

    /**
     * Set the meta keywords.
     */
    public function keywords(array $keywords): self
    {
        return $this->addMeta('keywords', implode(', ', $keywords));
    }

    /**
     * Set the robots.
     *
     * Supported values:
     * - `index`
     * - `noindex`
     * - `follow`
     * - `nofollow`
     * - `noarchive`
     * - `noimageindex`
     * - `nosnippet`
     */
    public function robots(string|array $robots): self
    {
        return $this->addMeta('robots', is_array($robots) ? implode(', ', $robots) : $robots);
    }

    /**
     * Set the canonical URL.
     */
    public function canonical(string $url): self
    {
        return $this->addLink('canonical', $url);
    }

    /**
     * Set the AMP HTML URL.
     */
    public function ampHtml(string $url): self
    {
        return $this->addLink('amphtml', $url);
    }

    /**
     * Set the previous page URL (for pagination).
     */
    public function prev(string $url, bool $condition = true): self
    {
        return $condition ? $this->addLink('prev', $url) : $this;
    }

    /**
     * Set the next page URL (for pagination).
     */
    public function next(string $url, bool $condition = true): self
    {
        return $condition ? $this->addLink('next', $url) : $this;
    }

    /**
     * Return the meta tags as an array.
     */
    public function toArray(): array
    {
        return [
            'meta' => $this->meta,
            'links' => $this->links,
            'languages' => $this->languages,
        ];
    }

    /**
     * Render the meta tags to HTML.
     */
    public function toHtml(bool $minify = false): string
    {
        $output = [
            "<title>{$this->title}</title>",
        ];

        foreach ($this->meta as $name => $content) {
            $output[] = sprintf('<meta name="%s" content="%s" />', $name, $content);
        }

        foreach ($this->links as $rel => $href) {
            $output[] = sprintf('<link rel="%s" href="%s" />', $rel, $href);
        }

        foreach ($this->languages as $hreflang => $href) {
            $output[] = sprintf('<link rel="alternate" hreflang="%s" href="%s" />', $hreflang, $href);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }
}
