<?php

declare(strict_types=1);

namespace Larament\SeoKit\Concerns;

use Larament\SeoKit\Data\SeoData;
use Larament\SeoKit\Facades\SeoKit;

/**
 * @phpstan-ignore trait.unused
 */
trait HasSeoData
{
    /**
     * Map the model's attributes to the SeoData DTO.
     */
    abstract public function toSeoData(): SeoData;

    /**
     * Prepares and applies SEO tags using the model's SEO data from the `toSeoData()` method.
     */
    public function prepareSeoTags(): void
    {
        $data = $this->toSeoData();

        if (empty(array_filter(get_object_vars($data)))) {
            return;
        }

        SeoKit::fromSeoData($data);
    }
}
