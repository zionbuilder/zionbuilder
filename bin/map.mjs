export const filesMap = [
  // Global
  {
    input: 'src/vue.ts',
    format: 'iife',
    output: 'vue',
    name: 'vue',
  },
  //   admin
  {
    input: 'src/admin/edit-page.ts',
    format: 'iife',
    output: 'edit-page',
    name: 'editPage',
  },
  {
    input: 'src/admin/gutenberg.ts',
    format: 'iife',
    output: 'gutenberg',
  },
  {
    input: 'src/admin/admin-page.ts',
    format: 'iife',
    output: 'admin-page',
    name: 'adminPage',
  },

  {
    input: 'src/modules/screenshot/index.ts',
    format: 'iife',
    output: 'screenshot',
  },
  //   {
  //     input: 'src/modules/integrations/rankmath.ts',
  //     format: 'iife',
  //     output: 'integrations/rankmath',
  //   },
  {
    input: 'src/modules/integrations/yoast.ts',
    format: 'iife',
    output: 'integrations/yoast',
  },
  // Editor
  {
    input: 'src/editor/editor.ts',
    format: 'iife',
    output: 'editor',
  },
  {
    input: 'src/preview/index.ts',
    format: 'iife',
    output: 'preview',
  },

  // packages
  {
    input: 'src/modules/animateJS/index.ts',
    format: 'iife',
    output: 'animateJS',
  },

  // Frontend
  {
    input: 'src/frontend/modules/video/index.ts',
    format: 'iife',
    output: 'ZBVideo',
  },
  {
    input: 'src/frontend/modules/videoBG/index.ts',
    format: 'iife',
    output: 'ZBVideoBg',
  },

  // Elements
  {
    input: 'src/elements/Section/editor.js',
    format: 'iife',
    output: 'elements/Section/editor',
  },
  {
    input: 'src/elements/Column/editor.js',
    format: 'iife',
    output: 'elements/Column/editor',
  },
];
