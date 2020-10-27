module.exports = (config) => {
	const customLoaderOptions = (config.css || {}).loaderOptions || {}
	const MiniCssExtractPlugin = require('mini-css-extract-plugin');

	return {
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].css',
				moduleFilename: ({name}) => {
					return `css/${name}.css`
				},
			})
		],
		module: {
			rules: [{
				test: /\.s[ac]ss$/i,
				use: [
					{
						loader: require('mini-css-extract-plugin').loader,
						options: {
							hmr: true
						}
					},
					// Creates `style` nodes from JS strings
					// 'style-loader',
					// Translates CSS into CommonJS
					{
						loader: require.resolve('css-loader')
					},
					// Compiles Sass to CSS
					{
						loader: 'sass-loader',
						options: customLoaderOptions.sass || {}
					},
				],
			}, ],
		},
	}
}
