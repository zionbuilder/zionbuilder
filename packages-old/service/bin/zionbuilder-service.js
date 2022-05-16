#!/usr/bin/env node

const Service = require('../lib/Service')
const service = new Service(process.cwd())

const rawArgv = process.argv.slice(2)
const args = require('minimist')(rawArgv, {
	boolean: [
		// build
		'baseline'
	]
})

const command = args._[0]

service.run(command, args, rawArgv).catch(err => {
	console.error(err)
	process.exit(1)
})
