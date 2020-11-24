module.exports = (config) => {
	return {
		devServer: {
			contentBase: './dist',
			hot: true,
		}
	}
}