#!/usr/bin/env node

const path = require('path')
const { exec } = require('child_process')
const args = [path.resolve('vendor/bin/phpcbf')].concat(
	process.argv.slice(2)
).join(' ')

exec(args, (error, stdout, stderr) => {
	if (error) {
		if (error.code < 2) {
			console.log(`stderr: ${stdout}`)
			process.exit(0)
		} else {
			process.exit(1)
		}
	}
	if (stderr) {
		console.log(`stderr: ${stderr}`)
		process.exit(1)
	}

	console.log(`stderr: ${stdout}`)
	process.exit(0)
})
