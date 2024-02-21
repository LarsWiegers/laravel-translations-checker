import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Laravel translation checker",
  description: "A laravel package that helps with translations",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Quick start', link: '/tutorials/quick-start' }
    ],

    sidebar: [
      { text: 'Supported versions', link: '/supported-versions' },
      {
        text: 'Tutorials',
        items: [
          { text: 'Quick start', link: '/tutorials/quick-start' },
          { text: 'Running in CI', link: '/tutorials/running-in-ci' },

        ]
      },
      {
        text: 'Features',
        items: [
          { text: 'Only defined in 1 language', link: '/features/between-languages' },
          { text: 'Not defined but used in blade', link: '/features/in-blade' },
          { text: 'Remove unused translations', link: '/features/remove-unused' },
          { text: 'Generate missing translations', link: '/features/generate' },
        ]
      },
      {
        text: 'Contributing',
        items: [
          { text: 'Guide', link: '/features/between-languages' },
          { text: 'Documentation', link: '/features/between-languages' },
          { text: 'Features', link: '/features/between-languages' },
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/LarsWiegers/laravel-translations-checker' },
      { icon: 'twitter', link: 'https://twitter.com/LarsWiegers' },
    ]
  }
})
