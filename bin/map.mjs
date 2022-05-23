export const filesMap = [
  // admin
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
  {
    input: 'src/modules/integrations/rankmath.ts',
    format: 'iife',
    output: 'integrations/rankmath',
  },
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
];
