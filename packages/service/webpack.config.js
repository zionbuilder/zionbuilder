let service = process.ZIONBUILDER_SERVICE

if (!service) {
	const Service = require('./lib/Service')
	service = new Service(process.env.ZIONBUILDER_CLI_CONTEXT || process.cwd())
	service.init(process.env.ZIONBUILDER_CLI_MODE || process.env.NODE_ENV)
}

module.exports = service.resolveWebpackConfig()
