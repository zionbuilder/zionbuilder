import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
	build: {
		target: 'es2015',
		// generate manifest.json in outDir
		manifest: true,
		rollupOptions: {
			// overwrite default .html entry
			input: {
				admin: './src/admin/main.ts',
				editor: './src/editor/main.ts',
				['edit-page']: './src/modules/edit-page/main.ts',
				['gutenberg']: './src/modules/gutenberg/main.ts',
			},
			output: {
				assetFileNames: `[name].[ext]`,
				entryFileNames: `[name].js`,
				globals: {
					'@zb/i18n': 'zb.l18n',
					'@zb/rest': 'zb.rest',
					vue: 'zb.vue',
				},
			},
			external: ['@zb/rest', 'vue'],
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				additionalData: `@import "./src/scss/_mixins.scss";`,
			},
		},
	},
	plugins: [vue()],
});
