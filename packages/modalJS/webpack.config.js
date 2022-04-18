const { getConfig } = require('@zionbuilder/webpack-config');
const path = require('path');

module.exports = getConfig(
	{},
	{
		resolve: {
			alias: {
				'@composables': path.resolve(__dirname, './src/composables'),
				'@components': path.resolve(__dirname, './src/components'),
			},
		},
	},
);
