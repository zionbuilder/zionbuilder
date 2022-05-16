const { getConfig } = require('@zionbuilder/webpack-config');
const path = require('path');
const glob = require('glob');

const configs = [];

const entry = {};
const integrationsPath = path.resolve(__dirname, './src');
glob.sync(`${integrationsPath}/*.ts`).forEach(file => {
	const fileInfo = path.parse(file);
	entry[`integrations/${fileInfo.name}`] = file;
});

configs.push(
	getConfig(
		{},
		{
			entry,
			output: {
				filename: `js/[name].js`,
			},
		},
	),
);

module.exports = configs;
