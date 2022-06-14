import { build } from 'vite';
import vue from '@vitejs/plugin-vue';
import { filesMap } from './map.mjs';
import path from 'path';
import { minify } from 'rollup-plugin-esbuild';
import { generateManifest } from './manifest.mjs';

import fs from 'fs';

// TODO: clean dist folder before start
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
      //   lib: {
      // 	entry: script.input,
      // 	name: script.name,
      // 	fileName: (format) => `${script.output}.js`,
      // 	formats: ['iife']
      //   },

      rollupOptions: {
        input: {
          [script.output]: script.input,
        },
        external: ['vue'],
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
          },
        },
      },
    },
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `@import "../src/common/scss/_mixins.scss";`,
        },
      },
    },
    plugins: [vue()],
  });
});

generateManifest({
  debug: false,
});
