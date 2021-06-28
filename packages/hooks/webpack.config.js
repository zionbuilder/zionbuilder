const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({
	css: {},
	features: {
		vue: true,
		zionVue: true
	}
})
