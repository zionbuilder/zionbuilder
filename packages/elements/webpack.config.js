const {
	getConfig
} = require('@zionbuilder/webpack-config');
const path = require('path')
const glob = require('glob')

const configs = []

	const entry = {}
	const elementPath = path.resolve(__dirname, './src')
	glob.sync(`${elementPath}/*/*.*`).forEach((file) => {
		const fileInfo = path.parse(file)
		const elementType = path.basename( fileInfo.dir )

		entry[`elements/${elementType}/${fileInfo.name}`] = file
	})

	configs.push(
		getConfig({
			css: {
				loaderOptions: {
					sass: {
						additionalData: `@import "~@zionbuilder/css-variables";`
					}
				}
			},
			features: {
				vue: true,
				zionVue: true
			}
		},
		{
			entry,
			output: {
				filename: `js/[name].js`
			}
		})
	)

module.exports = configs