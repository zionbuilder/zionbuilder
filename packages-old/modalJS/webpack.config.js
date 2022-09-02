const { getConfig } = require('@zionbuilder/webpack-config');

module.exports = getConfig(
	{},
	{
		entry: {
			modalJS: './src/index.ts',
		},
	},
);
