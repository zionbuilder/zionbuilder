const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({},
{
	entry: {
		ZBVideo: './src/index.ts',
		ZBVideoBg: './src/modules/videoBg.js'
	},
	output: {
		filename: '[name].js',
		library: '[name]',
		libraryTarget: 'umd',
		libraryExport: 'default'
	}
}
)
