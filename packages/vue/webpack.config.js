const {
	getConfig
} = require('@zionbuilder/webpack-config');
const { DefinePlugin } = require('webpack')

module.exports = getConfig({}, {
	plugins: [
		new DefinePlugin({
			__VUE_OPTIONS_API__: true,
			__VUE_PROD_DEVTOOLS__: false
		})
	]
})