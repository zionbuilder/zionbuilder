const context = process.cwd()
const path = require('path')

module.exports = {
	mode: "production",
	context: context,
	entry: './src/index.ts',
	resolve: {
		extensions: ['.ts', '.tsx', '.js', 'vue']
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				exclude: /node_modules/,
				loader: "ts-loader",
				options: {
					appendTsSuffixTo: [/\.vue$/],
					transpileOnly: true,
				}
			}
		],
	},
	output: {
		filename: 'bundle.js',
		path: path.resolve(context, 'dist'),
	}
}