<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class SeoKitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('seokit')
            ->hasConfigFile()
            ->hasMigration('create_seokit_table')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('larament/seokit');
            });
    }

    public function bootingPackage(): void
    {
        Blade::directive('seoKit', fn (bool $minify = false): string => "<?php echo \Larament\SeoKit\Facades\SeoKit::toHtml($minify); ?>");
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SeoKitManager::class, fn (): SeoKitManager => new SeoKitManager(
            new MetaTags,
            new OpenGraph,
            new TwitterCards,
            new JsonLD
        ));
    }

    public function packageBooted(): void
    {
        //
    }
}
