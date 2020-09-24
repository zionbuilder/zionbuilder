module.exports = (config) => {
	const customLoaderOptions = (config.css || {}).loaderOptions || {}

	return {
		module: {
			rules: [{
				test: /\.s[ac]ss$/i,
				use: [
					// Creates `style` nodes from JS strings
					'style-loader',
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
