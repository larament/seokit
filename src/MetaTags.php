<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Support\Facades\URL;
use Larament\SeoKit\Support\Util;

final class MetaTags
{
    private ?string $title = null;

    private array $meta = [];

    private array $links = [];

    private array $languages = [];

    public function __construct(private array $config)
    {
        $this->setDefaultValues();
    }

    public function addMeta(string $name, string $content): self
    {
        $this->meta[$name] = e($content);

        return $this;
    }

    public function removeMeta(string $name): self
    {
        unset($this->meta[$name]);

        return $this;
    }

    public function addLink(string $rel, string $href): self
    {
        $this->links[$rel] = e($href);

        return $this;
    }

    public function removeLink(string $rel): self
    {
        unset($this->links[$rel]);

        return $this;
    }

    public function addLanguage(string $hreflang, string $href): self
    {
        $this->languages[$hreflang] = e($href);

        return $this;
    }

    public function removeLanguage(string $hreflang): self
    {
        unset($this->languages[$hreflang]);

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = Util::formatTitle(
            Util::cleanString($title)
        );

        return $this;
    }

    public function description(string $description): self
    {
        return $this->addMeta('description', $description);
    }

    public function keywords(array $keywords): self
    {
        return $this->addMeta('keywords', implode(', ', $keywords));
    }

    /**
     * Set the robots.
     *
     * Supported values:
     *
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

    public function canonical(string $url): self
    {
        return $this->addLink('canonical', $url);
    }

    public function ampHtml(string $url): self
    {
        return $this->addLink('amphtml', $url);
    }

    public function prev(string $url, bool $condition = true): self
    {
        return $condition ? $this->addLink('prev', $url) : $this;
    }

    public function next(string $url, bool $condition = true): self
    {
        return $condition ? $this->addLink('next', $url) : $this;
    }

    public function toArray(): array
    {
        return [
            'meta' => $this->meta,
            'links' => $this->links,
            'languages' => $this->languages,
        ];
    }

    public function render(bool $minify = false): string
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

    /**
     * Set default values from the package configuration.
     * Merges provided config with default values.
     */
    private function setDefaultValues(): void
    {
        $config = array_merge([
            'title' => null,
            'before_title' => null,
            'after_title' => null,
            'title_separator' => ' - ',
            'description' => null,
            'robots' => 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1',
        ], $this->config);

        $this->title(Util::getTitleFromUrl() ?? $config['title']);

        if ($config['description']) {
            $this->description($config['description']);
        }

        match ($config['canonical']) {
            null => $this->canonical(URL::current()),
            'full' => $this->canonical(URL::full()),
            default => null,
        };

        if ($config['robots']) {
            $this->robots($config['robots']);
        }

        $this->config = $config;
    }
}
