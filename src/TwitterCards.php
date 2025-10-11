<?php

declare(strict_types=1);

namespace Larament\SeoKit;

final class TwitterCards
{
    private array $properties = [];

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

    public function card(string $card): self
    {
        $validCards = ['summary', 'summary_large_image', 'app', 'player'];

        if (! in_array($card, $validCards)) {
            $card = 'summary';
        }

        return $this->add('card', $card);
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

    public function toHtml(bool $minify = false): string
    {
        $output = [];

        foreach ($this->properties as $property => $value) {
            $output[] = sprintf('<meta name="twitter:%s" content="%s" />', $property, $value);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }
}
