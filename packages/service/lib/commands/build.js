const webpack = require('webpack')
const path = require('path')
const { DefinePlugin } = require('webpack')
const {
    info,
    done
} = require('../util')

module.exports = (options, args) => {
    const service = process.ZIONBUILDER_SERVICE

    // Generate the manifest
    service.generateManifest()

    return new Promise((resolve, reject) => {
        info('ZionBuilder Service is building files.')

        // Webpack
		const configPath = service.resolve('webpack.config.js')
		const webpackConfig = require(configPath)

        const applyDynamicPublicPathToEntries = function(entries) {
            Object.keys(entries).forEach(entry => {
                const entryValue = entries[entry]

                entries[entry] = [ path.resolve(__dirname, '../util/dynamicPublicPath.js'), entryValue]
            })
		}

		function applyDefines(config) {
			config.plugins = [
				...(config.plugins || []),
				new DefinePlugin({
					__ZIONBUILDER__: JSON.stringify({
						appName: webpackConfig.name
					})
				})
			]
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