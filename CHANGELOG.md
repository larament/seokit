# Changelog

All notable changes to `seokit` will be documented in this file.

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
