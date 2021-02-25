const fs = require('fs')
const path = require('path')
const chalk = require('chalk')
const Options = require('./Options')
const resolvePkg = require('./util/resolvePkg').resolvePkg
const getPort = require('get-port')
const {
	error,
	info,
	done
} = require('./util')

module.exports = class Service {
	constructor(context, {
		pkg
	} = {}) {
		this.context = context
		this.options = new Options(this.resolveConfig(), {
			outputDir: this.resolve(this.context, './dist/'),
			assetsDir: this.resolve(this.context, './dist/'),
		})

		this.webpackChainFns = []

		// Folder containing the target package.json for plugins
		this.pkgContext = context
		// package.json containing the plugins
		this.pkg = this.resolvePkg(pkg)

		// Add user chain webpack
		const userChainWebpack = this.options.getOption('chainWebpack')
		if (userChainWebpack && typeof userChainWebpack === 'function') {
			this.webpackChainFns.push(userChainWebpack)
		}

		process.ZIONBUILDER_SERVICE = this

		this.commands = {
			serve: require('./commands/serve'),
			build: require('./commands/build'),
			zip: require('./commands/zip'),
			translate: require('./commands/translate')
		}
	}

	resolvePkg(inlinePkg, context = this.context) {
		if (inlinePkg) {
			return inlinePkg
		}
		const pkg = resolvePkg(context)
		if (pkg.vuePlugins && pkg.vuePlugins.resolveFrom) {
			this.pkgContext = path.resolve(context, pkg.vuePlugins.resolveFrom)
			return this.resolvePkg(null, this.pkgContext)
		}
		return pkg
	}

	resolveConfig() {
		let fileConfig

		const configPath = (
			path.resolve(this.context, 'zionbuilder.config.js')
		)

		if (fs.existsSync(configPath)) {
			try {
				fileConfig = require(configPath)

				if (typeof fileConfig === 'function') {
					fileConfig = fileConfig()
				}

				if (!fileConfig || typeof fileConfig !== 'object') {
					console.error(
						`Error loading ${chalk.bold('zionbuilder.config.js')}: should export an object or a function that returns object.`
					)
					fileConfig = null
				}
			} catch (e) {
				console.error(`Error loading ${chalk.bold('zionbuilder.config.js')}:`)
				throw e
			}
		}

		return fileConfig
	}

	setEnvironmentMode(environmentMode = 'production') {
		process.env.NODE_ENV = environmentMode
	}

	async run(name, args, rawArgv) {
		const command = this.commands[name]

		// Set the proper mode
		const environmentMode = name === 'build' ? 'production' : 'development'
		this.setEnvironmentMode(environmentMode)

		args._.shift() // remove command itself
		rawArgv.shift()

		this.availablePort = await getPort()

		return command(this.options, args)
	}

	init(environmentMode = 'production') {
		this.setEnvironmentMode(environmentMode)
	}

	chainWebpack(fn) {
		this.webpackChainFns.push(fn)
	}

	getPublicPath() {
		const publicPath = this.options.getOption('publicPath')

		if (publicPath) {
			return publicPath
		}

		return `http://localhost/`
	}

	resolve(requestedPath) {
		return path.resolve(this.context, requestedPath)
	}

	generateManifest(extraData = {}) {
		const outputFilePath = this.resolve('manifest.json')
		let data = {
			appName: this.pkg.name,
			outputDir: 'dist',
			...extraData
		}

		fs.writeFile(outputFilePath, JSON.stringify(data), function (err) {
			if (err) return error(err);
			info('Manifest file written!')
		});
	}
}
