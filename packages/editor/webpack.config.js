const {
	getConfig
} = require('@zionbuilder/webpack-config');
const path = require('path')

module.exports = getConfig(
	{
		css: {
			loaderOptions: {
				sass: {
					additionalData: `@import "~@zionbuilder/css-variables";`
				}
			}
		},
		features:
		{
			vue: true,
			zionVue: true
		}
	},
	{
		resolve: {
			alias: {
				'@data': path.resolve(__dirname, './src/data'),
				'@components': path.resolve(__dirname, './src/components'),
				'@utils': path.resolve(__dirname, './src/utils')
			}
		}
	}

)