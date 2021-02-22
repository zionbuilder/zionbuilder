module.exports = (webpackConfig, service) => {
	const path = require('path')
	const options = service.options
	const outputDir = options.getOption('outputDir', './dist/')
	const publicPath = service.getPublicPath()

	const resolveLocal = require('../util/resolveLocal')
	const inlineLimit = 4096

	const genAssetSubPath = dir => {
		return `${dir}/[name]${options.filenameHashing ? '.[hash:8]' : ''}.[ext]`

	}

	const genUrlLoaderOptions = dir => {
		return {
			limit: inlineLimit,
			// publicPath: './',
			// use explicit fallback to avoid regression in url-loader>=1.1.0
			fallback: {
				loader: require.resolve('file-loader'),
				options: {
					name: genAssetSubPath(dir),
					esModule: false
				}
			}
		}
	}

	const relativePath = path.relative(service.context, outputDir)
	webpackConfig
		.mode('production')
		.context(service.context)
		.output
		.filename('[name].js')
		.path(service.resolve(outputDir))
		.publicPath(publicPath)

	webpackConfig
		.performance
		.hints(false)

	webpackConfig.resolve
		// This plugin can be removed once we switch to Webpack 6
		.plugin('pnp')
		.use({
			...require('pnp-webpack-plugin')
		})
		.end()
		.extensions
		.merge(['.mjs', '.js', '.jsx', '.vue', '.json', '.wasm'])
		.end()
		.modules
		.add('node_modules')
		.add(service.resolve('node_modules'))
		.add(resolveLocal('node_modules'))
		.end()
		.alias
		.set('@', service.resolve('src'))
		.set(
			'vue$',
			options.runtimeCompiler ?
			'vue/dist/vue.esm.js' :
			'vue/dist/vue.runtime.esm.js'
		)

	webpackConfig.resolveLoader
		.plugin('pnp-loaders')
		.use({
			...require('pnp-webpack-plugin').topLevelLoader
		})
		.end()
		.modules
		.add('node_modules')
		.add(service.resolve('node_modules'))
		.add(resolveLocal('node_modules'))

	webpackConfig.module
		.noParse(/^(vue|vue-router|vuex|vuex-router-sync)$/)

	// vue-loader --------------------------------------------------------------
	const vueLoaderCacheIdentifier = {
		'vue-loader': require('vue-loader/package.json').version
	}

	// The following 2 deps are sure to exist in Vue 2 projects.
	// But once we switch to Vue 3, they're no longer mandatory.
	// (In Vue 3 they are replaced by @vue/compiler-sfc)
	// So wrap them in a try catch block.
	try {
		vueLoaderCacheIdentifier['@vue/component-compiler-utils'] =
			require('@vue/component-compiler-utils/package.json').version
		vueLoaderCacheIdentifier['vue-template-compiler'] =
			require('vue-template-compiler/package.json').version
	} catch (e) {}
	const vueLoaderCacheConfig = service.genCacheConfig('vue-loader', vueLoaderCacheIdentifier)

	webpackConfig.module
		.rule('vue')
		.test(/\.vue$/)
		.use('cache-loader')
		.loader(require.resolve('cache-loader'))
		.options(vueLoaderCacheConfig)
		.end()
		.use('vue-loader')
		.loader(require.resolve('vue-loader'))
		.options(Object.assign({
			compilerOptions: {
				whitespace: 'condense'
			}
		}, vueLoaderCacheConfig))

	webpackConfig
		.plugin('vue-loader')
		.use(require('vue-loader/lib/plugin'))

	// static assets -----------------------------------------------------------
	webpackConfig.module
		.rule('images')
		.test(/\.(png|jpe?g|gif|webp)(\?.*)?$/)
		.use('url-loader')
		.loader(require.resolve('url-loader'))
		.options(genUrlLoaderOptions('img'))

	// do not base64-inline SVGs.
	// https://github.com/facebookincubator/create-react-app/pull/1180
	webpackConfig.module
		.rule('svg')
		.test(/\.(svg)(\?.*)?$/)
		.use('file-loader')
		.loader(require.resolve('file-loader'))
		.options({
			name: genAssetSubPath('img'),
			esModule: false
		})

	webpackConfig.module
		.rule('media')
		.test(/\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/)
		.use('url-loader')
		.loader(require.resolve('url-loader'))
		.options(genUrlLoaderOptions('media'))

	webpackConfig.module
		.rule('fonts')
		.test(/\.(woff2?|eot|ttf|otf)(\?.*)?$/i)
		.use('url-loader')
		.loader(require.resolve('url-loader'))
		.options(genUrlLoaderOptions('fonts'))

	// shims
	webpackConfig.node
		.merge({
			// prevent webpack from injecting useless setImmediate polyfill because Vue
			// source contains it (although only uses it if it's native).
			setImmediate: false,
			// process is injected via DefinePlugin, although some 3rd party
			// libraries may require a mock to work properly (#934)
			process: 'mock',
			// prevent webpack from injecting mocks to Node native modules
			// that does not make sense for the client
			dgram: 'empty',
			fs: 'empty',
			net: 'empty',
			tls: 'empty',
			child_process: 'empty'
		})

	const resolveClientEnv = require('../util/resolveClientEnv')
	webpackConfig
		.plugin('define')
		.use(require('webpack').DefinePlugin, [{
			'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV),
			__ZIONBUILDER__: JSON.stringify({
				appName: service.pkg.name
			})
		}])

	webpackConfig.externals([
		function (context, request, callback) {
			if (/^@zb\/.*$/.test(request)) {
				const modules = request.replace('@', '').split('/')
				// Externalize to a commonjs module using the request path
				return callback(null, modules, 'root');
			}

			callback()
		}
	])

	webpackConfig
		.plugin('case-sensitive-paths')
		.use(require('case-sensitive-paths-webpack-plugin'))

	// friendly error plugin displays very confusing errors when webpack
	// fails to resolve a loader, so we provide custom handlers to improve it
	const {
		transformer,
		formatter
	} = require('../util/resolveLoaderError')
	webpackConfig
		.plugin('friendly-errors')
		.use(require('@soda/friendly-errors-webpack-plugin'), [{
			additionalTransformers: [transformer],
			additionalFormatters: [formatter]
		}])
}
