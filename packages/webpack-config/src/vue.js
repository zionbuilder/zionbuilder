const { VueLoaderPlugin } = require('vue-loader')
const { DefinePlugin } = require('webpack')

module.exports = () => {
	return  {
		module: {
			rules: [
				{
					test: /\.vue$/,
					loader: "vue-loader"
				}
			]
		},
		plugins: [
			// make sure to include the plugin!
			new VueLoaderPlugin(),
			new DefinePlugin({
				__VUE_OPTIONS_API__: true,
				__VUE_PROD_DEVTOOLS__: false
			})
		]
	}
}