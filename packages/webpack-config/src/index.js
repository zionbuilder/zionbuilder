const {
	merge
} = require('webpack-merge');
const {
	readdirSync,
	existsSync
} = require('fs')

const path = require('path')

module.exports.mergeConfigs = function(...configs) {
	return merge(...configs)
}

module.exports.getConfig = function getConfig(options = {}, config = {}) {
	options = {
		...options,
		features: {
			js: true,
			css: true,
			...(options.features || {})
		},
	}

	const configs = []
	const baseConfig = require('./base')

	Object.keys(options.features).forEach(feature => {
		const enabled = options.features[feature]

		if (! enabled) {
			return
		}

		const featureExists = existsSync( path.resolve( __dirname, `${feature}.js`) )

		if (featureExists) {
			const getFeatureConfig = require(`./${feature}`)
			configs.push(getFeatureConfig(options, config))
		} else {
			console.warn(`The feature ${feature} does not exist!`)
		}
	})

	return merge(baseConfig(), ...configs, config)
}
