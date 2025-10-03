<?php

declare(strict_types=1);

namespace Larament\SeoKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Larament\SeoKit\SeoKitManager
 */
final class SeoKit extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Larament\SeoKit\SeoKitManager::class;
    }
}
