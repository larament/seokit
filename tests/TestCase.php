<?php

declare(strict_types=1);

namespace Larament\SeoKit\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Larament\SeoKit\SeoKitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Larament\\SeoKit\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function getEnvironmentSetUp($app)
    {
        // import the config file
        $app['config']->set('seokit', require __DIR__.'/../config/seokit.php');

        config()->set('seokit.auto_title_from_url', false);
        config()->set('database.default', 'testing');
        
        // Use array cache driver to avoid database table issues in tests
        config()->set('cache.default', 'array');

        foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }

    }

    protected function getPackageProviders($app)
    {
        return [
            SeoKitServiceProvider::class,
        ];
    }
}