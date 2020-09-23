const webpack = require('webpack')
const path = require('path')
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

        service.chainWebpack(webpackConfig => {
            webpackConfig.mode('production')

            if (service.options.getOption('writeStats', false) === true) {
                const { BundleStatsWebpackPlugin } = require('bundle-stats-webpack-plugin');
                webpackConfig
                    .plugin('bundle-stats-webpack-plugin')
                    .use(new BundleStatsWebpackPlugin({
                        baseline: true
                    }))
            }

            // webpackConfig
            //     .plugin('clean-webpack-plugin')
            //     .use(require('clean-webpack-plugin').CleanWebpackPlugin)

        })

        // Webpack
        const webpackConfig = service.resolveWebpackConfig()
   
        const applyDynamicPublicPathToEntries = function(entries) {
            Object.keys(entries).forEach(entry => {
                const entryValue = entries[entry]

                entries[entry] = [ path.resolve(__dirname, '../util/dynamicPublicPath.js'), entryValue]
            })
        }

        applyDynamicPublicPathToEntries(webpackConfig.entry)

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