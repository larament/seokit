# Installation

Install SeoKit into your Laravel project via Composer.

## Step 1: Require the Package

Run the following command in your project root:

```bash
composer require larament/seokit
```

## Step 2: Run the Installer

SeoKit provides an interactive artisan command to publish configuration files, migrations, and run database migrations automatically:

```bash
php artisan seokit:install
```

This interactive command will:
1. Publish the configuration file (`config/seokit.php`).
2. Publish database migration files (`database/migrations/*_create_seokit_table.php`).
3. Prompt you to run the database migration immediately.

---

## Manual Installation (Alternative)

If you prefer to publish package assets manually without the interactive prompt, you can use standard `vendor:publish` commands:

### Publish Configuration

```bash
php artisan vendor:publish --tag="seokit-config"
```

This creates `config/seokit.php`, where you can adjust site-wide defaults, title separators, social sharing accounts, and structured data settings.

### Publish and Run Migrations

If you plan to use database-backed SEO with Eloquent models:

```bash
php artisan vendor:publish --tag="seokit-migrations"
php artisan migrate
```

::: tip Skip Migrations if Not Using Database Storage
If your application only sets SEO metadata dynamically in controllers or via computed model attributes (`HasSeoData`), you do not need to run the database migration.
:::

---

## Service Provider & Discovery

SeoKit registers its service provider `Larament\SeoKit\SeoKitServiceProvider` and `SeoKit` facade automatically through Laravel's package auto-discovery mechanism.

If your application has disabled auto-discovery, manually register the provider in `bootstrap/providers.php` (Laravel 11+) or `config/app.php`:

```php
return [
    // Other Service Providers
    Larament\SeoKit\SeoKitServiceProvider::class,
];
```

## Next Steps

Now that SeoKit is installed, head over to the [Quick Start Guide](/getting-started/quick-start) to see how easy it is to manage metadata in your controllers and Blade views.
