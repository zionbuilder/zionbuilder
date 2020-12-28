const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({
		css: {
			loaderOptions: {
				sass: {
					additionalData: `@import "~@zionbuilder/css-variables/_variables.scss";@import "~@zionbuilder/css-variables/_mixins.scss";`
				}
			}
		},
		features: {
			vue: true
		}
	}

)
