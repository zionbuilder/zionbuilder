import { build } from 'vite';
import vue from '@vitejs/plugin-vue';
import { minify } from 'rollup-plugin-esbuild';
// import { resolve, dirname } from 'path';
import { filesMap } from './map.mjs';

// TODO: clean dist folder before start
filesMap.forEach(async script => {
  await build({
    resolve: {
      alias: {
        'vue-base': '../node_modules/vue/index.mjs',
      },
    },
    // mode: 'production',
    build: {
      cssCodeSplit: false,
      emptyOutDir: false,
      outDir: '../dist',
      target: 'es2015',
      // generate manifest.json in outDir
      //   manifest: true,
      //   minify: false,
      rollupOptions: {
        input: {
          [script.output]: script.input,
          gutenberg: '../src/admin/gutenberg.ts',
        },
        output: {
          assetFileNames: `[name].[ext]`,
          entryFileNames: `[name].js`,
          //   format: script.format,
          globals: {
            '@zb/i18n': 'zb.l18n',
            '@zb/rest': 'zb.rest',
            '@zb/hooks': 'zb.hooks',
            '@zb/utils': 'zb.utils',
            vue: 'zb.vue',
          },
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
        external: ['@zb/rest', '@zb/i18n', '@zb/hooks', '@zb/utils', 'vue'],
      },
    },
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `@import "../src/common/scss/_mixins.scss";`,
        },
      },
    },
    plugins: [
      //   vue(),
      // externalGlobals({
      // 	'@zb/i18n': 'zb.l18n',
      // 	'@zb/rest': 'zb.rest',
      // 	'@zb/hooks': 'zb.hooks',
      // 	'@zb/utils': 'zb.utils',
      // 	vue: 'zb.vue',
      // }),
    ],
  });
});
