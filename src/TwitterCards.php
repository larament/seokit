<?php

declare(strict_types=1);

namespace Larament\SeoKit;

final class TwitterCards
{
    private array $properties = [];

    public function __construct(private array $config)
    {
        $this->prepareDefaults();
    }

    public function add(string $property, string|int $value): self
    {
        $this->properties[$property] = e($value);

        return $this;
    }

    public function remove(string $property): self
    {
        unset($this->properties[$property]);

        return $this;
    }

    public function title(string $title): self
    {
        return $this->add('title', $title);
    }

    public function type(string $type): self
    {
        $validTypes = ['summary', 'summary_large_image', 'app', 'player'];

        if (! in_array($type, $validTypes)) {
            $type = 'summary';
        }

        return $this->add('card', $type);
    }

    public function site(string $username): self
    {
        return $this->add('site', $username);
    }

    public function creator(string $username): self
    {
        return $this->add('creator', $username);
    }

    public function description(string $description): self
    {
        return $this->add('description', $description);
    }

    public function image(string $url, ?string $alt = null): self
    {
        $this->add('image', $url);

        if ($alt) {
            $this->add('image:alt', $alt);
        }

        return $this;
    }

    public function player(string $url, int $width, int $height): self
    {
        return $this->add('player', $url)
            ->add('player:width', $width)
            ->add('player:height', $height);
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    public function render(bool $minify = false): string
    {
        $output = [];

        foreach ($this->properties as $property => $value) {
            $output[] = sprintf('<meta name="twitter:%s" content="%s" />', $property, $value);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }

    private function prepareDefaults(): void
    {
        $config = array_merge([
            'card' => 'summary_large_image',
            'site' => null,
            'creator' => null,
        ], $this->config);

        if ($config['card']) {
            $this->type($config['card']);
        }

        if ($config['site']) {
            $this->site($config['site']);
        }

        if ($config['creator']) {
            $this->creator($config['creator']);
        }

        $this->config = $config;
    }
}
