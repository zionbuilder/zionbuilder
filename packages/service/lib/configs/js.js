const path = require('path')
const babel = require('@babel/core')

module.exports = (webpackConfig, service) => {
	const useThreads = process.env.NODE_ENV === 'production' && !!service.options.getOption('parallel')
	webpackConfig.resolveLoader.modules.prepend(path.join(__dirname, 'node_modules'))

	const babelOptions = {
		presets: [require.resolve('@zionbuilder/babel-preset-zionbuilder')]
	}

	const jsRule = webpackConfig.module
	  .rule('js')
		.test(/\.m?jsx?$/)
		.exclude
			.add(filepath => {
				// always transpile js in vue files
				if (/\.vue\.jsx?$/.test(filepath)) {
					return false
				}

				// Don't transpile node_modules
				return /node_modules/.test(filepath)
			})
			.end()
		.use('cache-loader')
			.loader(require.resolve('cache-loader'))
			.options(service.genCacheConfig('babel-loader', {
				'@babel/core': require('@babel/core/package.json').version,
				'@zionbuilder/babel-preset-zionbuilder': require('@zionbuilder/babel-preset-zionbuilder').version,
				'babel-loader': require('babel-loader/package.json').version,
				browserslist: service.pkg.browserslist
			}, [
				'babel.config.js',
				'.browserslistrc'
			]))
			.end()

	if (useThreads) {
		const threadLoaderConfig = jsRule
			.use('thread-loader')
			.loader(require.resolve('thread-loader'))

		if (typeof options.parallel === 'number') {
			threadLoaderConfig.options({ workers: options.parallel })
		}
	}

	jsRule
		.use('babel-loader')
			.tap(() => babelOptions)
			.loader(require.resolve('babel-loader'))
}