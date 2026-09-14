# Changelog

All notable changes to `seokit` will be documented in this file.

## v1.7.0 - 2026-09-14

### v1.7.0 - 2026-09-14

#### What's Changed

##### 🚀 Added

* **Default Image Storage Disk Configuration**: Added a `'disk'` configuration option in `config/seokit.php` (supporting the `SEOKIT_DISK` environment variable). When `null`, it seamlessly inherits the host application's default filesystem disk (`config('filesystems.default')`).
* **Automatic Model Persistence Hook**: Relative image paths saved on the `Seo` model (`$post->seo()->create([...])`) now automatically persist the configured default disk to `og_image_disk` and `twitter_image_disk`, eliminating the need to specify the disk on every record creation while preserving explicit overrides.
* **Automatic Disk Cleanup**: Updating an image to an external URL (`https://...`) or removing it automatically resets the corresponding disk column to `null`, ensuring the database never retains stale storage metadata.

##### 🛠️ Enhanced

* **Streamlined URL Detection**: Replaced custom URL prefix checks with Laravel's `Str::isUrl()` across image resolution and persistence hooks.

**Full Changelog**: https://github.com/larament/seokit/compare/v1.6.0...v1.7.0

## v1.6.0 - 2026-09-14

### What's Changed

#### 🚀 Added

* **Dedicated Documentation Website**: Built a modern, markdown-driven documentation website using VitePress, deployed automatically via Bun to GitHub Pages.
* **Storage Disk Resolution for Images**: Added support for `og_image_disk` and `twitter_image_disk` in migrations, models, and `SeoData`, automatically resolving absolute image URLs across local, public, and S3 storage disks.
* **Typed `MetaRobots` Enum**: `MetaTags::robots()` now accepts instances and arrays of the `MetaRobots` enum in addition to strings.
* **Laravel Octane & FrankenPHP Compatibility**: Scoped `SeoKitManager` within the container to ensure request isolation and prevent cross-request metadata leakage in persistent worker environments.
* **Dynamic Blade Directive**: `@seoKit` now compiles expressions dynamically, enabling runtime minification checks (e.g. `@seoKit(app()->isProduction())`).

#### 🛠️ Fixed

* **JSON-LD Script Breakout**: Added `JSON_HEX_TAG` when encoding structured data to prevent XSS / script breakout attacks.
* **Soft Delete Safety**: Preserved polymorphic `seo` relationship records when the parent model is soft-deleted; records are now only pruned on force-delete.
* **Cache Invalidation Null Safety**: Added model existence guards before purging cache keys during SEO record deletion.
* **Twitter Card Attributes**: Enforced automatic `@` handle prefixing and prevented double-encoding of Twitter metadata.
* **Open Graph Escaping**: Fixed attribute escaping and ensured non-title content tags are not prematurely stripped.
* **String Cleaning**: Corrected tag stripping order in `Util::cleanString` (`e(strip_tags(...))`).
* **Keyword Parsing**: Improved whitespace trimming and comma-separated keyword extraction in `fromSeoData()`.

#### 🧹 Chores & Maintenance

* Redesigned package SVG cover graphic.
* Bumped CI dependencies (`actions/checkout` to v7, `codecov/codecov-action` to v7).

**Full Changelog**: https://github.com/larament/seokit/compare/v1.5.0...v1.6.0

## v1.5.0 - 2026-05-19

### What's Changed

* chore(deps): bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 by @dependabot[bot] in https://github.com/larament/seokit/pull/9

**Full Changelog**: https://github.com/larament/seokit/compare/v1.4.0...v1.5.0

## v1.4.0 - 2026-04-12

### Added

- Meta keywords support (`keywords` column in migration, `SeoData` field, and `MetaTags::keywords()`).
- SeoKit integration skill for AI agents (Laravel boost).

### Enhanced

- Updated guidelines with full API reference and `SeoData` fields.
- 

**Full Changelog**: https://github.com/larament/seokit/compare/v1.3.1...v1.4.0

## v1.3.1 - 2026-04-10

### Fixed

- `HasSeo::prepareSeoTags()` now applies fallback SEO data even when no SEO relationship record exists.

### Added

- Regression test coverage for fallback-only SEO rendering when the SEO relation is missing.

### Notes

- Existing merge behavior remains: non-empty SEO relationship values override fallback values.

## v1.3.0 - 2026-04-10

### Added

- Model-defined SEO fallback support in `HasSeo` via `fallbackSeoData()`.

### Changed

- `prepareSeoTags()` now merges database SEO values with model fallback data.
- Improved `SeoData` handling with an `isEmpty()` helper for cleaner checks.

### Tests

- Updated `HasSeo` tests to cover user-defined fallback values and fixture constraints.

## v1.2.0 - 2026-03-30

#### New Features

- Added Laravel 13 support.

#### Documentation

- Added a new `assets/cover.svg` package cover image.
- Refactored `README.md` for a shorter, wiki-first package overview.

#### Testing & CI

- Added Orchestra Testbench 11 to the development support matrix.
- Changed CI test execution to use Pest directly.
- Reduced the minimum coverage threshold from 100% to 95%.
- Updated GitHub Action dependencies (`ramsey/composer-install`, `codecov/codecov-action`, and `dependabot/fetch- metadata`).

#### Maintenance

- Removed the dev-only `nunomaduro/collision` constraint to avoid blocking Laravel 13 dependency resolution.
- Small internal refactors and test cleanup for the Laravel 13 support work.

**Full Changelog**: https://github.com/larament/seokit/compare/v1.1.0...v1.2.0  ### New Features

- Added Laravel 13 support.

### Documentation

- Added a new `assets/cover.svg` package cover image.
- Refactored `README.md` for a shorter, wiki-first package overview.

#### Testing & CI

- Added Orchestra Testbench 11 to the development support matrix.
- Changed CI test execution to use Pest directly.
- Reduced the minimum coverage threshold from 100% to 95%.
- Updated GitHub Action dependencies (`ramsey/composer-install`, `codecov/codecov-action`, and `dependabot/fetch- metadata`).

#### Maintenance

- Removed the dev-only `nunomaduro/collision` constraint to avoid blocking Laravel 13 dependency resolution.
- Small internal refactors and test cleanup for the Laravel 13 support work.

**Full Changelog**: https://github.com/larament/seokit/compare/v1.1.0...v1.2.0

## Unreleased

### What's Changed

- Added Laravel 13 support.
- Added Orchestra Testbench 11 to the development support matrix.
- Removed the dev-only `nunomaduro/collision` constraint to avoid blocking Laravel 13 dependency resolution.

## v1.1.0 - 2026-03-02

### What's Changed

#### 🚀 New Features

- Added **Inertia.js** route detection and automatic title rendering updates.
- Added `canonical()` method to the `SeoKit` facade for easier canonical URL management.

#### 🛠️ Refactoring & Improvements

- Removed `spatie/laravel-package-tools` dependency to reduce overhead.
- Improved type safety with comprehensive PHP type hints across the codebase.
- Updated `site_name` configuration to default to `config('app.name')`.

#### 🧹 Maintenance

- Code styling fixes and internal cleanup.
- Updated GitHub Action dependencies (checkout, fetch-metadata, git-auto-commit).

#### 📝 Full Changelog

https://github.com/larament/seokit/compare/v1.0.1...v1.1.0

## v1.0.1 - 2025-10-11

**Full Changelog**: https://github.com/larament/seokit/compare/v1.0...v1.0.1

## v1.0 - 2025-10-11

**Full Changelog**: https://github.com/larament/seokit/compare/v0.2...v1.0

## v0.2 - 2025-10-06

**Full Changelog**: https://github.com/larament/seokit/compare/v0.1...v0.2
