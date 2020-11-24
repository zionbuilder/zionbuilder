module.exports = (options, args) => {
	const service = process.ZIONBUILDER_SERVICE
	const webpack = require('webpack')
	const url = require('url')
	const { error, info, done } = require('../util')
	const port = service.availablePort
	const defaults = {
		host: 'localhost'
	}
	const host = service.options.getOption('host') || defaults.host
	const hostForCLI = url.format({
		protocol: 'http',
		hostname: host,
		port,
		pathname: '/'
	})

	// Generate the manifest file
	service.generateManifest({
		debug: true,
		mode: 'development',
		devServer: hostForCLI
	})

	service.chainWebpack(webpackConfig => {
		webpackConfig.mode('development')

		webpackConfig
			.devtool('cheap-module-eval-source-map')
		webpackConfig
			.stats('errors-warnings')

		// https://github.com/webpack/webpack/issues/6642
		// https://github.com/vuejs/vue-cli/issues/3539
		webpackConfig
			.output
			.globalObject(`(typeof self !== 'undefined' ? self : this)`)

		webpackConfig
			.plugin('hmr')
			.use(require('webpack/lib/HotModuleReplacementPlugin'))

		if (options.getOption('progress', true) !== false) {
			webpackConfig
				.plugin('progress')
				.use(require('webpack/lib/ProgressPlugin'))
		}
	})

	const webpackConfig = service.resolveWebpackConfig()
	const devServerOptions = {
		logLevel: 'silent',
		clientLogLevel: 'silent',
		stats: false,
		logTime: false,
		headers: {
			'Access-Control-Allow-Origin': '*'
		},
		allowedHosts: ['*'],
		disableHostCheck: true,
		overlay: {
			warnings: false,
			errors: true
		},
		port,
		hot: true,
		hotOnly: true,
		injectClient: true,
		injectHot: true,
		writeToDisk (filePath) {
			// We need to write the files to disk so plugin cache system can compile the assets
			// Only write our own files to disk
			return !/hot-update\.(json|js)$/.test(filePath)
		}
	}

	webpackConfig.output.publicPath = hostForCLI

	// create compiler
	const compiler = webpack(webpackConfig)

	// create dev server
	const WebpackDevServer = require('webpack-dev-server')
	const server = new WebpackDevServer(compiler, Object.assign(
		devServerOptions,
		webpackConfig.devServer || {}
	))

	const closeServer = function() {
		// Generate the manifest file
		service.generateManifest({
			debug: false
		})

		server.close(() => {
			process.exit(0)
		})
	}

	;['SIGINT', 'SIGTERM'].forEach(signal => {
		process.on(signal, () => {
			closeServer()
		})
	})
  
	if (args.stdin) {
		process.stdin.on('end', () => {
			closeServer()
		})
  
		process.stdin.resume()
	}

	return new Promise((resolve, reject) => {

		compiler.hooks.done.tap('vue-cli-service serve', stats => {
			info(`  App running at: ${hostForCLI}`)
		})

		server.listen(port, host, err => {
			if (err) {
				reject(err)
			}
		})
	})
}