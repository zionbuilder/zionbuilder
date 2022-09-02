module.exports = (config) => {
	const customLoaderOptions = (config.css || {}).loaderOptions || {}
	const MiniCssExtractPlugin = require('mini-css-extract-plugin');

	return {
		plugins: [
			new MiniCssExtractPlugin({
				filename: 'css/[name].css'
			})
		],
		module: {
			rules: [{
					// find these extensions in our css, copy the files to the outputPath,
					// and rewrite the url() in our css to point them to the new (copied) location
					test: /\.(woff(2)?|eot|otf|ttf|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
					use: {
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: 'fonts',
							publicPath: '../fonts'
						}
					}
				},
				{
					test: /\.css$/i,
					use: [{
							loader: require('mini-css-extract-plugin').loader,
						},
						{
							loader: require.resolve('css-loader'),
							options: {
								url: false
							}
						}
					],
				},
				{
					test: /\.s[ac]ss$/i,
					use: [{
							loader: require('mini-css-extract-plugin').loader
						},
						// Translates CSS into CommonJS
						{
							loader: require.resolve('css-loader'),
							options: {
								url: true
							}
						},
						// Compiles Sass to CSS
						{
							loader: 'sass-loader',
							options: customLoaderOptions.sass || {}
						},
					],
				},
			],
		},
	}
}
