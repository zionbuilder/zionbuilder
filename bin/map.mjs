import path from 'path';
import fs from 'fs';
import glob from 'glob';

// Gather all elements files
// const elementsPackage = path.resolve('./src/elements');
// console.log(elementsPackage);
const elementsMap = [];
fs.readdirSync('src/elements/').forEach(file => {
  console.log(file);
});

// glob('src/elements/*/*.js', function (er, files) {
//   // files is an array of filenames.
//   // If the `nonull` option is set, and nothing
//   // was found, then files is ["**/*.js"]
//   // er is an error object or null.
//   console.log(files);
// });

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
  {
    input: 'src/elements/Video/editor.js',
    format: 'iife',
    output: 'elements/Video/editor',
  },
  {
    input: 'src/elements/Video/frontend.ts',
    format: 'iife',
    output: 'elements/Video/frontend',
  },
];
