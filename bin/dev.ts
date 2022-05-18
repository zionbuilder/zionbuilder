const path = require('path');
const express = require('express');
const vue = require('@vitejs/plugin-vue');
const { createServer: createViteServer } = require('vite');
console.log(path.resolve(__dirname, '../src/modules/hooks/index.ts'));

const files = ['./a.js', './b.js'];

async function createServer() {
	const app = express();

	// Create Vite server in middleware mode.
	files.forEach(async file => {
		console.log(file);
		const vite = await createViteServer({
			configFile: false,

			resolve: {
				alias: {
					'vue-base': path.resolve(__dirname, './node_modules/vue/index.mjs'),
				},
			},
			build: {
				// lib: {
				// 	entry: path.resolve(__dirname, '../src/modules/hooks/index.es.ts'),
				// 	name: 'MyLib',
				// },
				target: 'es2015',
				// generate manifest.json in outDir
				manifest: true,
				minify: false,
				rollupOptions: {
					// overwrite default .html entry
					input: './a.js',
					output: {
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
			plugins: [vue()],

			server: { middlewareMode: 'html' },
		});
		console.log(vite.middlewares);
		// Use vite's connect instance as middleware
		app.use(vite.middlewares);
	});

	app.use('*', async (req, res) => {
		// If `middlewareMode` is `'ssr'`, should serve `index.html` here.
		// If `middlewareMode` is `'html'`, there is no need to serve `index.html`
		// because Vite will do that.
	});

	const appInstance = app.listen(1337, function () {
		console.log(`'Server listening at: ${appInstance.address().port}`);
		console.log(appInstance.address());
	});

	return app;
}

createServer();
