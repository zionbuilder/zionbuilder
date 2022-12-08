import { build } from 'vite';
import vue from '@vitejs/plugin-vue';
import { filesMap } from './map.mjs';
import path from 'path';
import { minify } from 'rollup-plugin-esbuild';
import { generateManifest } from './manifest.mjs';
import fs from 'fs';

// Clear the dist folder
const dist = path.resolve('./dist');
fs.rmSync(dist, { recursive: true, force: true });

filesMap.forEach(async script => {
  await build({
    mode: 'production',
    resolve: {
      alias: {
        '/@': path.resolve('./src'),
        '/@zb/vue': path.resolve('./node_modules/vue'),
      },
    },
    build: {
      minify: false,
      cssCodeSplit: false,
      emptyOutDir: false,
      target: 'es2015',
      rollupOptions: {
        input: {
          [script.output]: script.input,
        },
        external: ['vue', '@zb/common', '@zb/hooks', '@zb/i18n', '@zb/api', '@zb/common', '@zb/utils'],
        output: {
          name: script.name,
          entryFileNames: `[name].js`,
          assetFileNames: assetInfo => {
            // This is required because vite forces the name style.css if we disable code splitting
            if (assetInfo.name == 'style.css') return `${script.output}.css`;
            return `[name].[ext]`;
          },
          format: 'iife',
          globals: {
            vue: 'zb.vue',
            '@zb/common': 'zb.common',
            '@zb/hooks': 'zb.hooks',
            '@zb/i18n': 'zb.i18n',
            '@zb/api': 'zb.api',
            '@zb/utils': 'zb.utils',
          },
        },
      },
    },
    plugins: [vue()],
  });
});

generateManifest({
  debug: false,
});
