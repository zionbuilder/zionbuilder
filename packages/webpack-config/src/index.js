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

module.exports.getConfig = function getConfig(features = { js: true, css: true }, config = {}) {
	const configs = []
	const baseConfig = require('./base')

	Object.keys(features).forEach(feature => {
		const enabled = features[feature]

		if (! enabled) {
			return
		}

		const featureExists = existsSync( path.resolve( __dirname, `${feature}.js`) )

		if (featureExists) {
			const featureConfig = require(`./${feature}`)
			configs.push(featureConfig)
		} else {
			console.warn(`The feature ${feature} does not exist!`)
		}
	})

	return merge(baseConfig, ...configs, config)
}
