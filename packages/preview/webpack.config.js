const {
	getConfig
} = require('@zionbuilder/webpack-config');
const path = require('path')

module.exports = getConfig({
		features: {
			vue: true,
			zionVue: true
		}
	}, {
		resolve: {
			alias: {
				'@composables': path.resolve(__dirname, './src/composables'),
				'@components': path.resolve(__dirname, './src/components'),
				'@utils': path.resolve(__dirname, './src/utils')
			}
		}
	}

)
