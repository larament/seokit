<?php

declare(strict_types=1);

namespace Larament\SeoKit\Data;

final readonly class SeoData
{
    public function __construct(
        public string $title = '',
        public string $description = '',
        public ?string $canonical = null,
        public ?string $robots = null,
        public string $og_title = '',
        public string $og_description = '',
        public string $og_image = '',
        public ?string $twitter_image = null,
        public ?array $structured_data = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['canonical'] ?? null,
            $data['robots'] ?? null,
            $data['og_title'] ?? '',
            $data['og_description'] ?? '',
            $data['og_image'] ?? '',
            $data['twitter_image'] ?? null,
            $data['structured_data'] ?? null,
        );
    }
}
