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
        Blade::directive('seoKit', function (bool $minify = false) {
            return "<?php echo app(\Larament\SeoKit\SeoKitManager::class)->toHtml($minify); ?>";
        });
    }

    public function packageRegistered(): void
    {
        //
    }

    public function packageBooted(): void
    {
        //
    }
}
