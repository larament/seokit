# Introduction

**SeoKit** is an all-in-one technical SEO toolkit for Laravel applications. It unifies meta tags, Open Graph properties, Twitter cards, and Schema.org JSON-LD structured data under one clean, expressive API.

## The Problem SeoKit Solves

Search Engine Optimization (SEO) and social media sharing tags are fundamental to any modern web application. However, implementing them properly in Laravel often leads to friction:

1. **Fragmented Packages**: Developers frequently end up installing 3 or 4 disparate packages—one for meta tags, another for Open Graph, another for Twitter, and another for Schema JSON-LD.
2. **Scattered Metadata**: Tags get duplicated or inconsistently set between controllers, Blade `@section` directives, and Eloquent model observers.
3. **Database Bloat**: Teams either clutter domain models with 15+ SEO columns (such as `meta_title`, `og_image`, `twitter_description`, `schema_json`) or struggle to implement clean polymorphic SEO relationships with caching.
4. **State Leakage in Long-Running Apps**: In Laravel Octane or FrankenPHP setups, un-scoped singletons can bleed metadata across concurrent HTTP requests.

SeoKit addresses every one of these problems with a unified architecture.

## Core Capabilities

- **Unified Facade**: Manage all metadata layers through `Larament\SeoKit\Facades\SeoKit`.
- **Single Blade Directive**: Output all necessary tags with `@seoKit`, or selectively render specific sections (e.g. `@seoKit('meta')`, `@seoKit('opengraph')`).
- **Dynamic URL Title Inference**: Automatically generate human-readable titles from the current URL path when an explicit title hasn't been set.
- **Polymorphic Database-Backed SEO**: Add a `seo` polymorphic relationship to any Eloquent model with the `HasSeo` trait, including automated cache invalidation.
- **Computed Model SEO**: Map domain model attributes to SEO metadata dynamically via the `HasSeoData` trait and `toSeoData()` method.
- **Rich Social Sharing**: Full support for Open Graph video, audio, article authoring, and Twitter card player/app variants.
- **Schema.org Structured Data**: Fluent builders for Search Engine JSON-LD graphs (Articles, Products, Organizations, BreadcrumbLists, etc.).
- **Octane & FrankenPHP Compatible**: Service provider bindings are properly scoped per request lifecycle to guarantee zero state leakage.

## Requirements

Before installing SeoKit, ensure your project meets the following requirements:

| Dependency | Supported Versions |
| --- | --- |
| **PHP** | `^8.3` |
| **Laravel** | `11.x`, `12.x`, `13.x` |

## Next Steps

Ready to get started? Proceed to the [Installation Guide](/getting-started/installation) to install SeoKit into your Laravel application.
