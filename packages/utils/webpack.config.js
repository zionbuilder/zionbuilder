const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({
	features: {
		vue: false,
		zionVue: true
	}
})