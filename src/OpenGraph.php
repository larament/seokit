<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Support\Facades\URL;

final class OpenGraph
{
    private array $properties = [];

    private array $images = [];

    private array $videos = [];

    private array $audios = [];

    public function __construct(private array $config = [])
    {
        $this->prepareDefaults();
    }

    public function add(string $property, string $content): self
    {
        $this->properties[$property] = e($content);

        return $this;
    }

    public function remove(string $property): self
    {
        unset($this->properties[$property]);

        return $this;
    }

    public function title(string $title): self
    {
        return $this->add('og:title', $title);
    }

    public function type(string $type): self
    {
        $validTypes = [
            'website', 'article', 'book', 'profile', 'music.song',
            'music.album', 'music.playlist', 'music.radio_station',
            'video.movie', 'video.episode', 'video.tv_show', 'video.other',
        ];

        if (! in_array($type, $validTypes)) {
            $type = 'website';
        }

        return $this->add('og:type', $type);
    }

    public function url(string $url): self
    {
        return $this->add('og:url', $url);
    }

    public function description(string $description): self
    {
        return $this->add('og:description', $description);
    }

    public function siteName(string $name): self
    {
        return $this->add('og:site_name', $name);
    }

    public function locale(string $locale): self
    {
        return $this->add('og:locale', $locale);
    }

    public function image(
        string $url,
        ?string $secureUrl = null,
        ?string $type = null,
        ?int $width = null,
        ?int $height = null,
        ?string $alt = null
    ): self {
        $index = count($this->images);
        $this->images[] = compact('url', 'secureUrl', 'type', 'width', 'height', 'alt');

        $this->add('og:image', $url);

        if ($secureUrl) {
            $this->add('og:image:secure_url', $secureUrl);
        }

        if ($type) {
            $this->add('og:image:type', $type);
        }

        if ($width) {
            $this->add('og:image:width', (string) $width);
        }

        if ($height) {
            $this->add('og:image:height', (string) $height);
        }

        if ($alt) {
            $this->add('og:image:alt', $alt);
        }

        return $this;
    }

    public function video(
        string $url,
        ?string $secureUrl = null,
        ?string $type = null,
        ?int $width = null,
        ?int $height = null
    ): self {
        $index = count($this->videos);
        $this->videos[] = compact('url', 'secureUrl', 'type', 'width', 'height');

        $this->add('og:video', $url);

        if ($secureUrl) {
            $this->add('og:video:secure_url', $secureUrl);
        }

        if ($type) {
            $this->add('og:video:type', $type);
        }

        if ($width) {
            $this->add('og:video:width', (string) $width);
        }

        if ($height) {
            $this->add('og:video:height', (string) $height);
        }

        return $this;
    }

    public function audio(string $url, ?string $secureUrl = null): self
    {
        $this->audios[] = ['url' => $url, 'secureUrl' => $secureUrl];

        $this->add('og:audio', $url);

        if ($secureUrl) {
            $this->add('og:audio:secure_url', $secureUrl);
        }

        return $this;
    }

    public function article(
        ?string $publishedTime = null,
        ?string $modifiedTime = null,
        ?string $expirationTime = null,
        ?array $authors = null,
        ?string $section = null,
        ?array $tags = null
    ): self {
        $this->type('article');

        if ($publishedTime) {
            $this->add('article:published_time', $publishedTime);
        }

        if ($modifiedTime) {
            $this->add('article:modified_time', $modifiedTime);
        }

        if ($expirationTime) {
            $this->add('article:expiration_time', $expirationTime);
        }

        if ($authors) {
            foreach ($authors as $author) {
                $this->add('article:author', $author);
            }
        }

        if ($section) {
            $this->add('article:section', $section);
        }

        if ($tags) {
            foreach ($tags as $tag) {
                $this->add('article:tag', $tag);
            }
        }

        return $this;
    }

    public function profile(
        string $firstName,
        string $lastName,
        ?string $username = null,
        ?string $gender = null
    ): self {
        $this->type('profile');

        $this->add('profile:first_name', $firstName);
        $this->add('profile:last_name', $lastName);

        if ($username) {
            $this->add('profile:username', $username);
        }

        if ($gender && in_array($gender, ['male', 'female'])) {
            $this->add('profile:gender', $gender);
        }

        return $this;
    }

    public function book(
        ?string $author = null,
        ?string $isbn = null,
        ?string $releaseDate = null,
        ?array $tags = null
    ): self {
        $this->type('book');

        if ($author) {
            $this->add('book:author', $author);
        }

        if ($isbn) {
            $this->add('book:isbn', $isbn);
        }

        if ($releaseDate) {
            $this->add('book:release_date', $releaseDate);
        }

        if ($tags) {
            foreach ($tags as $tag) {
                $this->add('book:tag', $tag);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    public function render(bool $minify = false): string
    {
        $output = [];

        foreach ($this->properties as $property => $content) {
            $output[] = sprintf('<meta property="%s" content="%s" />', $property, $content);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }

    private function prepareDefaults(): void
    {
        $config = array_merge([
            'type' => 'website',
            'site_name' => config('app.name'),
            'locale' => config('app.locale', 'en_US'),
        ], $this->config);

        $this->type($config['type']);
        $this->siteName($config['site_name']);
        $this->locale($config['locale']);

        match ($config['url']) {
            null => $this->url(URL::current()),
            'full' => $this->url(URL::full()),
            default => null,
        };

        if (isset($config['title'])) {
            $this->title($config['title']);
        }

        if (isset($config['description'])) {
            $this->description($config['description']);
        }

        if (isset($config['image'])) {
            $this->image(
                $config['image'],
                $config['image_secure_url'] ?? null,
                $config['image_type'] ?? null,
                $config['image_width'] ?? null,
                $config['image_height'] ?? null,
                $config['image_alt'] ?? null
            );
        }

        $this->config = $config;
    }
}
