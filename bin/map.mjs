import path from 'path';

export const filesMap = [
  // admin
  {
    input: 'src/admin/edit-page.ts',
    format: 'iife',
    output: 'edit-page',
  },
  {
    input: 'src/admin/gutenberg.ts',
    format: 'iife',
    output: 'gutenberg',
  },
  {
    input: './src/admin/admin-page.ts',
    format: 'iife',
    output: 'admin-page',
  },
  // Common
  {
    input: './src/common/main.ts',
    format: 'iife',
    output: 'components',
  },
  // Modules
  {
    input: 'src/modules/hooks/index.ts',
    format: 'iife',
    output: 'hooks',
  },
  {
    input: './src/modules/i18n/index.ts',
    format: 'iife',
    output: 'i18n',
  },
  {
    input: './src/modules/rest/index.ts',
    format: 'iife',
    output: 'rest',
  },
  {
    input: './src/modules/vue/index.ts',
    format: 'iife',
    output: 'vue',
  },
  {
    input: './src/common/utils/index.ts',
    format: 'iife',
    output: 'utils',
  },
];
