import { build } from 'vite';
import vue from '@vitejs/plugin-vue';
import { filesMap } from './map.mjs';
import path from 'path';
import { generateManifest } from './manifest.mjs';
import fs from 'fs';
import { visualizer } from 'rollup-plugin-visualizer';

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
        '/@zb/pinia': path.resolve('./node_modules/pinia'),
        '/@zb/vue-router': path.resolve('./node_modules/vue-router'),
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
        external: [
          'vue',
          'pinia',
          'vue-router',
          '@zb/api',
          '@zb/components',
          '@zb/composables',
          '@zb/hooks',
          '@zb/store',
          '@zb/utils',
          '@wordpress/i18n',
        ],
        output: {
          name: script.name,
          entryFileNames: `[name].js`,
          assetFileNames: assetInfo => {
            // This is required because vite forces the name style.css if we disable code splitting
            if (assetInfo.name == 'style.css') return `${script.output}.css`;
            return `[name].[ext]`;
          },
          format: script.format ?? 'iife',
          globals: {
            vue: 'zb.vue',
            pinia: 'zb.pinia',
            ['vue-router']: 'zb.VueRouter',
            '@zb/api': 'zb.api',
            '@zb/components': 'zb.components',
            '@zb/composables': 'zb.composables',
            '@zb/hooks': 'zb.hooks',
            '@zb/store': 'zb.store',
            '@zb/utils': 'zb.utils',
            '@wordpress/i18n': 'wp.i18n',
          },
        },
      },
    },
    plugins: [
      vue(),
      visualizer({
        filename: `./stats/${script.input}.html`,
        open: false,
      }),
    ],
  });
});

generateManifest({
  debug: false,
});
