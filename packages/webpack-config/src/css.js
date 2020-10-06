const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (config) => {
	const customLoaderOptions = (config.css || {}).loaderOptions || {}

	return {
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].css',
				moduleFilename: ({name}) => {
					console.log(name)
					return `css/${name}.css`
				},
			})
		],
		module: {
			rules: [{
				test: /\.s[ac]ss$/i,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
						options: {
							hmr: true
						}
					},
					// Creates `style` nodes from JS strings
					// 'style-loader',
					// Translates CSS into CommonJS
					'css-loader',
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
