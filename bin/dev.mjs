import { createServer } from 'vite';
import vue from '@vitejs/plugin-vue';
// import { resolve, dirname } from 'path';
import { filesMap } from './map.mjs';
import path from 'path';
import { generateManifest } from './manifest.mjs';

const inputs = {};
filesMap.forEach(file => {
  inputs[file.name] = file.input;
});

const server = await createServer({
  configFile: false,
  resolve: {
    alias: {
      '@common': '/../src/common',
    },
  },
  mode: 'development',
  build: {
    rollupOptions: {
      input: inputs,
      external: ['@zb/rest', '@zb/i18n', '@zb/hooks', '@zb/utils', 'vue'],
      globals: {
        '@zb/i18n': 'zb.l18n',
        '@zb/rest': 'zb.rest',
        '@zb/hooks': 'zb.hooks',
        '@zb/utils': 'zb.utils',
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
