<?php

declare(strict_types=1);

namespace Larament\SeoKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Larament\SeoKit\MetaTags meta()
 * @method static \Larament\SeoKit\OpenGraph opengraph()
 * @method static \Larament\SeoKit\TwitterCards twitter()
 * @method static \Larament\SeoKit\JsonLD jsonld()
 * @method static \Larament\SeoKit\SeoKitManager fromSeoData(\Larament\SeoKit\Data\SeoData $data)
 * @method static \Larament\SeoKit\SeoKitManager title(string $title)
 * @method static \Larament\SeoKit\SeoKitManager description(string $description)
 * @method static \Larament\SeoKit\SeoKitManager image(string $image)
 * @method static \Larament\SeoKit\SeoKitManager canonical(string $canonical)
 * @method static string toHtml(bool $minify = false)
 *
 * @see \Larament\SeoKit\SeoKitManager
 */
final class SeoKit extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Larament\SeoKit\SeoKitManager::class;
    }
}
