import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'SeoKit',
  description: 'A complete Technical SEO toolkit for Laravel',
  base: process.env.VITEPRESS_BASE || '/',

  head: [
    ['meta', { name: 'theme-color', content: '#10B981' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:title', content: 'SeoKit - Technical SEO Made Easy in Laravel' }],
    ['meta', { property: 'og:description', content: 'A complete SEO package for Laravel, covering meta tags, social sharing, JSON-LD, and database-backed SEO.' }],
  ],

  themeConfig: {
    siteTitle: 'SeoKit',

    nav: [
      { text: 'Guide', link: '/getting-started/introduction' },
      { text: 'Configuration', link: '/getting-started/configuration' },
      { text: 'Packagist', link: 'https://packagist.org/packages/larament/seokit' },
      {
        text: 'v1.x',
        items: [
          { text: 'Changelog', link: 'https://github.com/larament/seokit/blob/main/CHANGELOG.md' },
          { text: 'Source Code', link: 'https://github.com/larament/seokit' }
        ]
      }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        collapsed: false,
        items: [
          { text: 'Introduction', link: '/getting-started/introduction' },
          { text: 'Installation', link: '/getting-started/installation' },
          { text: 'Quick Start', link: '/getting-started/quick-start' },
          { text: 'Configuration', link: '/getting-started/configuration' },
        ]
      },
      {
        text: 'Core Guide',
        collapsed: false,
        items: [
          { text: 'Meta Tags & Titles', link: '/guide/meta-tags' },
          { text: 'Open Graph', link: '/guide/open-graph' },
          { text: 'Twitter Cards', link: '/guide/twitter-cards' },
          { text: 'JSON-LD Structured Data', link: '/guide/json-ld' },
          { text: 'Blade Directives', link: '/guide/blade-directives' },
        ]
      },
      {
        text: 'Eloquent Integration',
        collapsed: false,
        items: [
          { text: 'Database-Backed SEO', link: '/models/database-backed-seo' },
          { text: 'Computed SEO Data', link: '/models/computed-seo' },
          { text: 'Images & Storage Disks', link: '/models/images-and-storage' },
        ]
      },
      {
        text: 'Advanced',
        collapsed: false,
        items: [
          { text: 'Macros & Extensions', link: '/advanced/macros-and-customization' },
          { text: 'Caching & Invalidation', link: '/advanced/caching' },
          { text: 'Laravel Octane Safety', link: '/advanced/octane' },
        ]
      }
    ],

    search: {
      provider: 'local',
      options: {
        detailedView: true
      }
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/larament/seokit' }
    ],

    editLink: {
      pattern: 'https://github.com/larament/seokit/edit/main/docs/:path',
      text: 'Edit this page on GitHub'
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2025-present Raziul Islam & Larament Contributors'
    }
  }
})
