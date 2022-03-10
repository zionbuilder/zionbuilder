const path = require('path')
const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({}, {
	entry: {
		animateJS: './src/index.ts'
	},
	output: {
		libraryExport: 'default',
		library: 'animateJS',
		libraryTarget: 'umd',
	},
})