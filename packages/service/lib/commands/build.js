const webpack = require('webpack')
const path = require('path')
const {
	DefinePlugin
} = require('webpack')
const {
	info,
	done
} = require('../util')

// const StatoscopeWebpackPlugin = require('@statoscope/webpack-plugin').default;

module.exports = (options, args) => {
	const service = process.ZIONBUILDER_SERVICE

	// Generate the manifest
	service.generateManifest()

	return new Promise((resolve, reject) => {
		info('ZionBuilder Service is building files.')

		// Webpack
		const configPath = service.resolve('webpack.config.js')
		const appConfigPath = service.resolve('package.json')
		const webpackConfig = require(configPath)
		const appConfig = require(appConfigPath)

		const applyDynamicPublicPathToEntries = function (entries) {
			Object.keys(entries).forEach(entry => {
				const entryValue = entries[entry]

				entries[entry] = [path.resolve(__dirname, '../util/dynamicPublicPath.js'), entryValue]
			})
		}

		function applyDefines(config) {
			config.plugins = [
				...(config.plugins || []),
				new DefinePlugin({
					__ZIONBUILDER__: JSON.stringify({
						appName: appConfig.name
					})
				}),
				// new StatoscopeWebpackPlugin({
				// 	reports: [{
				// 		id: 'top-20-biggest-modules',
				// 		name: 'Top 20 biggest modules',
				// 		view: [
				// 			'struct',
				// 			{
				// 				data: `#.stats.compilations.(
				// 				$compilation: $;
				// 				modules.({
				// 				  module: $,
				// 				  hash: $compilation.hash,
				// 				  size: getModuleSize($compilation.hash)
				// 				})
				// 			  ).sort(size.size desc)`,
				// 				view: 'list',
				// 				item: 'module-item',
				// 			},
				// 		],
				// 	}, ],
				// })
			]

			config.optimization = {
				minimize: true
			}
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
