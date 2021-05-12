const path = require('path')
const {
	getConfig
} = require('@zionbuilder/webpack-config');

module.exports = getConfig({}, {
	output: {
		libraryExport: 'default',
		library: 'animateJs',
		libraryTarget: 'umd',
	},
})

// const path = require('path');


// module.exports = {
//     entry: './src/js/animateJs.js',
//     output: {
//         path: path.resolve(__dirname, 'dist'),
//         publicPath: 'dist/',
//         filename: 'animateJs.js',
//         libraryExport: 'default',
//         library: 'animateJs',
//         libraryTarget: 'umd',
//     },
//     devServer: {
//         contentBase: 'demo/'
//     },
//     module: {
//         rules: [
//             {
//                 test: /\.js$/,
//                 exclude: /node_modules/,
//                 use: {
//                     loader: "babel-loader"
//                 }
//             }
//         ]
//     }
// }
