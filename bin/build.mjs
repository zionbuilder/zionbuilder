import { build } from 'vite';
import vue from '@vitejs/plugin-vue';
import { minify } from 'rollup-plugin-esbuild';
import { filesMap } from './map.mjs';
import path from 'path';
import { generateManifest } from './manifest.mjs';

// TODO: clean dist folder before start
filesMap.forEach(async script => {
  await build({
    mode: 'production',
    resolve: {
      alias: {
        '@common': path.resolve('./src/common'),
      },
    },
    build: {
      //   cssCodeSplit: false,
      emptyOutDir: false,
      target: 'es2015',
      rollupOptions: {
        input: {
          [script.output]: script.input,
        },
        output: {
          assetFileNames: `[name].[ext]`,
          entryFileNames: `[name].js`,
          //   format: script.format,
        },

        // output: [
        //   {
        //     assetFileNames: `[name].[ext]`,
        //     entryFileNames: `[name].js`,
        //     format: script.format,
        //     globals: {
        //       '@zb/i18n': 'zb.l18n',
        //       '@zb/rest': 'zb.rest',
        //       '@zb/hooks': 'zb.hooks',
        //       '@zb/utils': 'zb.utils',
        //       vue: 'zb.vue',
        //     },
        //   },
        //   //   {
        //   //     assetFileNames: `[name].min.[ext]`,
        //   //     entryFileNames: `[name].min.js`,
        //   //     compact: true,
        //   //     format: script.format,
        //   //     sourcemap: true,
        //   //     plugins: [
        //   //       minify({
        //   //         minify: true,
        //   //         minifyWhitespace: true,
        //   //         minifySyntax: true,
        //   //         treeShaking: true,
        //   //       }),
        //   //     ],
        //   //   },
        // ],
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
