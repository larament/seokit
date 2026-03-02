<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Larament\SeoKit\Console\Commands\InstallCommand;

final class SeoKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seokit.php', 'seokit');

        $this->app->singleton(SeoKitManager::class, fn (): SeoKitManager => new SeoKitManager(
            new MetaTags,
            new OpenGraph,
            new TwitterCards,
            new JsonLD
        ));
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerBladeDirective();
    }

    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/seokit.php' => config_path('seokit.php'),
            ], 'seokit-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_seokit_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_seokit_table.php'),
            ], 'seokit-migrations');
        }
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    private function registerBladeDirective(): void
    {
        Blade::directive('seoKit', fn (bool $minify = false): string => "<?php echo \Larament\SeoKit\Facades\SeoKit::toHtml($minify); ?>");
    }
}
