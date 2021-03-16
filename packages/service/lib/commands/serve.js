module.exports = (options, args) => {
	const webpack = require('webpack')
	const {
		DefinePlugin
	} = require('webpack')

	const {
		info,
		done
	} = require('../util')

	const url = require('url')


	const service = process.ZIONBUILDER_SERVICE
	const port = service.availablePort
	const {
		WebpackPluginServe
	} = require('webpack-plugin-serve');
	console.log(WebpackPluginServe);

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

	const serveinstance = new WebpackPluginServe({
		hmr: true,
		host: '127.0.0.1',
		port,
		static: '/dist',
		waitForBuild: true
	})

	return new Promise((resolve, reject) => {
		info('ZionBuilder Service is building files.')

		// Webpack
		const configPath = service.resolve('webpack.config.js')
		const webpackConfig = require(configPath)

		const applyDynamicPublicPathToEntries = function (entries) {
			Object.keys(entries).forEach(entry => {
				const entryValue = entries[entry]

				entries[entry] = ['webpack-plugin-serve/client', entryValue]
			})
		}

		let i = 0

		function applyDefines(config) {
			config.plugins = [
				i === 0 ? serveinstance : serveinstance.attach(),
				...(config.plugins || []),
				new DefinePlugin({
					__ZIONBUILDER__: JSON.stringify({
						appName: webpackConfig.name
					}),
					'process.env.NODE_ENV': JSON.stringify('development')
				})
			]

			// config.output.publicPath = hostForCLI

			config.optimization = {
				minimize: false
			}

			// Enable watch
			config.watch = true

			// Set development mode
			config.mode = 'development'
		}

		if (Array.isArray(webpackConfig)) {
			webpackConfig.forEach(config => {
				applyDynamicPublicPathToEntries(config.entry)
				applyDefines(config)
			})
		} else {
			applyDynamicPublicPathToEntries(webpackConfig.entry)
			applyDefines(webpackConfig)
		}

		webpack(webpackConfig, (err, stats) => {
			if (err) {
				console.error(err.stack || err);
				if (err.details) {
					console.error(err.details);
				}
				return;
			}

			const info = stats.toJson();

			if (stats.hasErrors()) {
				console.error(info.errors);
			}

			if (stats.hasWarnings()) {
				console.warn(info.warnings);
			}

			if (err) {
				return reject(err)
			}

			if (stats.hasErrors()) {
				return reject(`Build failed with errors.`)
			}

			done(`Build complete.`)

			resolve()
		})
	})

}
