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
			},
			output: {
				assetFileNames: `[name].[ext]`,
				entryFileNames: `[name].js`,
			},
		},
	},
	plugins: [vue()],
});
