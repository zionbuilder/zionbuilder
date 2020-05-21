#!/usr/bin/env node

const path = require('path')
const { exec } = require('child_process')
const args = [path.resolve('vendor/bin/phpcbf')].concat(
	process.argv.slice(2)
).join(' ')

console.log({ args })
exec(args, (error, stdout, stderr) => {
	if (error) {
		if (error.code < 2) {
			console.log(`stderr: ${stdout}`)
			process.exit(0)
		} else {
			console.log(`error333333: ${stdout}`)
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

// new Promise((resolve, reject) => {
// 	const args = [path.resolve(__dirname, './vendor/bin/phpcbf')].concat(
// 		process.argv.slice(2)
// 	)
// 	const phpcbf = exec('php', args)
// 	phpcbf.stdout.on('data', data => console.log(data.toString()))
// 	phpcbf.stderr.on('data', data => console.log(`stderr: ${data}`))
// 	phpcbf.on('close', code => (code < 2 ? resolve(code) : reject(code)))
// })
// 	.then(() => process.exit(0))
// 	.catch(() => process.exit(1))
