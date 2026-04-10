# Changelog

All notable changes to `seokit` will be documented in this file.

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
