const path = require('path')
const {
	mergeConfigs
} = require('@zionbuilder/webpack-config');

const {
	readdirSync,
	existsSync
} = require('fs')


const configs = []

const getDirectories = source =>
	readdirSync(source, {
		withFileTypes: true
	})
	.filter(dirent => dirent.isDirectory())
	.map(dirent => dirent.name)

const getWebpackConfig = (folder) => {
	const webpackFileLocation = path.resolve( folder, 'webpack.config.js' )
	return existsSync( webpackFileLocation ) ? webpackFileLocation : false
}

const packages = getDirectories('./packages')

packages.forEach(directory => {
	const folder = path.resolve('./packages', directory)
	const webpackConfig = getWebpackConfig(folder)

	if (webpackConfig) {
		const packageWebpackConfig = require(webpackConfig)
		const config = mergeConfigs(
			packageWebpackConfig,
			{
				// Change context to package folder so that webpack knows where to look for files
				context: folder,
				// Export all packages to window.zb
				output: {
					filename: `js/${directory}.js`,
					library: [ 'zb', directory ],
					libraryTarget: 'this'
				}
			}
		)

		configs.push(config)
	}
})

module.exports = configs
