<?php

declare(strict_types=1);

namespace Larament\SeoKit\Exceptions;

use RuntimeException;

final class InvalidImageUrlException extends RuntimeException
{
    public static function missingDisk(string $path): self
    {
        return new self(sprintf('The image path [%s] cannot be resolved because no storage disk was specified.', $path));
    }
}
