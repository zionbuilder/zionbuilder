const context = process.cwd()
const path = require('path')

module.exports = () => {
	return {
		mode: "production",
		context: context,
		entry: './src/index.ts',
		resolve: {
			extensions: ['.ts', '.tsx', '.js', 'vue'],
			alias: {
				'@': './packages'
			}
		},
		module: {
			rules: [{
					test: /\.tsx?$/,
					exclude: /node_modules/,
					loader: require.resolve('ts-loader'),
					options: {
						appendTsSuffixTo: [/\.vue$/],
						transpileOnly: true,
					}
				},
				// Fix browser errors for packaged source maps
				{
					test: /\.js$/,
					use: [{
						loader: require.resolve('source-map-loader')
					}]


				},
			],
		},
		output: {
			filename: 'bundle.js',
			path: path.resolve(context, 'dist'),
			// TODO: change this to prorper url
			publicPath: 'http://zionbuilder.test/wp-content/plugins/zionbuilder/dist/'
		},
		externals: [
			function ({
				context,
				request
			}, callback) {
				if (/^@zb\/.*$/.test(request)) {
					const modules = request.replace('@', '').split('/')
					// Externalize to a commonjs module using the request path
					return callback(null, modules, 'root');
				}
				callback()
			}
		]
	}
}
