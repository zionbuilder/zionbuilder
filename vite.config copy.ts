import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import externalGlobals from 'rollup-plugin-external-globals';
const path = require('path');
// import iife from 'rollup-plugin-iife';
import { terser } from 'rollup-plugin-terser';

// https://vitejs.dev/config/
export default defineConfig({
	resolve: {
		alias: {
			'vue-base': path.resolve(__dirname, './node_modules/vue/index.mjs'),
		},
	},
	build: {
		target: 'es2015',
		// generate manifest.json in outDir
		manifest: true,
		minify: false,
		rollupOptions: {
			// overwrite default .html entry
			input: {
				// Admin
				// admin: './src/admin/admin-page.ts',
				['edit-page']: './src/admin/edit-page.ts',
				['gutenberg']: './src/admin/gutenberg.ts',

				// Editor
				// editor: './src/editor/editor.ts',

				// Modules
				i18n: './src/modules/i18n/index.ts',
				vue: './src/modules/vue/index.ts',
				hooks: './src/modules/hooks/index.ts',
				rest: './src/modules/rest/index.ts',
				// rest: './src/modules/rest/main.ts',
				// composables: './src/modules/composables/main.ts',
				// components: './src/modules/components/main.ts',
				// sortable: './src/modules/sortable/index.ts',
				// utils: './src/modules/utils/index.ts',
				// ['edit-page']: './src/modules/edit-page/main.ts',
				// ['gutenberg']: './src/modules/gutenberg/main.ts',
			},
			output: [
				{
					assetFileNames: `[name].[ext]`,
					entryFileNames: `[name].js`,
					globals: {
						'@zb/i18n': 'zb.l18n',
						'@zb/rest': 'zb.rest',
						'@zb/hooks': 'zb.hooks',
						'@zb/utils': 'zb.utils',
						vue: 'zb.vue',
					},
				},
				{
					assetFileNames: `[name].min.[ext]`,
					entryFileNames: `[name].min.js`,
					plugins: [terser()],
				},
			],
			external: ['@zb/rest', '@zb/i18n', '@zb/hooks', '@zb/utils', 'vue'],
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				additionalData: `@import "./src/common/scss/_mixins.scss";`,
			},
		},
	},
	plugins: [
		vue(),
		// externalGlobals({
		// 	'@zb/i18n': 'zb.l18n',
		// 	'@zb/rest': 'zb.rest',
		// 	'@zb/hooks': 'zb.hooks',
		// 	'@zb/utils': 'zb.utils',
		// 	vue: 'zb.vue',
		// }),
	],
});
