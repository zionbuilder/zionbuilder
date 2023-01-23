import { createServer } from 'vite';
import vue from '@vitejs/plugin-vue';
// import { resolve, dirname } from 'path';
import { filesMap } from './map.mjs';
import path from 'path';
import { generateManifest } from './manifest.mjs';
import { viteExternalsPlugin } from 'vite-plugin-externals';

const inputs = {};
filesMap.forEach(file => {
  inputs[file.name] = file.input;
});

const server = await createServer({
  configFile: false,
  resolve: {
    alias: {
      '/@': path.resolve('./src'),
      '/@zb/vue': path.resolve('./node_modules/vue'),
      '/@zb/pinia': path.resolve('./node_modules/pinia'),
      '/@zb/vue-router': path.resolve('./node_modules/vue-router'),
    },
  },
  mode: 'development',
  build: {
    rollupOptions: {
      input: inputs,
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
      ],
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
      },
    },
  },
  //   css: {
  //     preprocessorOptions: {
  //       scss: {
  //         additionalData: `@import "../src/common/scss/_mixins.scss";`,
  //       },
  //     },w
  //   },
  plugins: [
    vue(),
    viteExternalsPlugin({
      vue: ['zb', 'vue'],
      pinia: ['zb', 'pinia'],
      ['vue-router']: ['zb', 'VueRouter'],
      '@zb/api': ['zb', 'api'],
      '@zb/components': ['zb', 'components'],
      '@zb/composables': ['zb', 'composables'],
      '@zb/hooks': ['zb', 'hooks'],
      '@zb/store': ['zb', 'store'],
      '@zb/utils': ['zb', 'utils'],
    }),
  ],
  server: {
    host: '127.0.0.1',
  },
});

await server.listen();

/**
 * Generate manifest file
 *
 * This will set the dev mode to on and also output a map of the file handles with their location
 */
const devScripts = {};
const { address, port } = server.httpServer.address();
const url = `http://${address}:${port}/`;
filesMap.forEach(fileConfig => {
  devScripts[fileConfig.output] = `${url}${fileConfig.input}`;
});
generateManifest({
  debug: true,
  devScripts,
});

server.printUrls();
