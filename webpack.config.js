const path = require('path')
const {
	mergeConfigs,
	getConfig
} = require('@zionbuilder/webpack-config');

const {
	readdirSync,
	existsSync
} = require('fs')


const configs = []

const getWebpackConfig = (folder) => {
	const webpackFileLocation = path.resolve(folder, 'webpack.config.js')
	return existsSync(webpackFileLocation) ? webpackFileLocation : false
}

const packages = [
	'animateJS',
	'admin',
	'components',
	'editor',
	'hooks',
	'i18n',
	'rest',
	'utils',
	'vue',
	'edit-page',
	'gutenberg',
	'preview',
	'rtl'
]

packages.forEach(directory => {
	const folder = path.resolve('./packages', directory)
	const webpackConfig = getWebpackConfig(folder)

	if (webpackConfig) {
		const packageWebpackConfig = require(webpackConfig)
		const config = mergeConfigs(
			packageWebpackConfig, {
				entry: {
					[directory]: packageWebpackConfig.entry
				},
				// Change context to package folder so that webpack knows where to look for files
				context: folder,
				// Export all packages to window.zb
				output: {
					filename: `js/${directory}.js`,
					library: ['zb', directory],
					libraryTarget: 'window'
				}
			}
		)

		configs.push(config)
	}
})

// CSS
const cssFiles = [
	'frontend'
]

cssFiles.forEach(entry => {
	const folder = path.resolve(`./packages/css-variables/${entry}/`)
	const scssFile = path.resolve(`./packages/css-variables/${entry}.scss`)

	configs.push(getConfig({}, {
		entry: {
			[entry]: scssFile
		},
		context: folder,
		// Export all packages to window.zb
		output: {
			filename: `js/${entry}.js`
		}
	}))
})


// Add elements config
const elementsPackage = path.resolve('./packages/elements')
const webpackConfigPath = getWebpackConfig(elementsPackage)
const elementsWebpackConfigFile = require(webpackConfigPath)

elementsWebpackConfigFile.forEach(singleElementConfig => {
	configs.push(mergeConfigs(singleElementConfig, {
		context: elementsPackage
	}))
})

module.exports = configs
