<?php

declare(strict_types=1);

namespace Larament\SeoKit\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'seokit:install', description: 'Install the Seokit package and publish the configuration')]
final class InstallCommand extends Command
{
    public function handle(): int
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->askToRunMigrations();
        $this->askToStarRepo('larament/seokit');

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'seokit-config',
        ]);
    }

    private function publishMigrations(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'seokit-migrations',
        ]);
    }

    private function askToRunMigrations(): void
    {
        if (confirm('Would you like to run the migrations now?', true)) {
            $this->call('migrate');
        }
    }

    private function askToStarRepo(string $repoVendorPath): void
    {
        if (confirm('Would you like to star this repo on GitHub?', true)) {
            $repoUrl = "https://github.com/{$repoVendorPath}";

            match (mb_strtolower(PHP_OS_FAMILY)) {
                'darwin' => exec("open {$repoUrl}"),
                'linux' => exec("xdg-open {$repoUrl}"),
                'windows' => exec("start {$repoUrl}"),
                default => null,
            };
        }

        $this->components->info('Thank you ❤️');
    }
}
