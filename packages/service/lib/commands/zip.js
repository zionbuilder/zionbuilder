module.exports = (options, args) => {
	const fs = require('fs')
	const buildCommand = require('./build')
	const translateCommand = require('./translate')
	const { error, info, done } = require('../util')
	const fse = require('fs-extra')
	var zip = require('bestzip')
    const execa = require('execa')
	const { exec } = require("child_process")
	const path = require('path')
	const service = process.ZIONBUILDER_SERVICE

	function createZip() {
		const cwd = process.cwd()
		const parentFolder = cwd.split(path.sep).pop()

		// Prepare files
		const filesForCopy = service.options.getOption('zipFiles', [])
		const filesForCopyIncludingFolder = filesForCopy.map((source) => {
			return `${parentFolder}${path.sep}${source}`
		})

		zip({
			source: filesForCopyIncludingFolder,
			destination: `${parentFolder}${path.sep}zion-builder.zip`,
			// Set the cwd to parent directory so we can include the folder name in the archive
			cwd: path.join(cwd, '..')
		}).then(function() {
			done('all done!');
		}).catch(function(err) {
			console.error(err.stack);
			process.exit(1);
		});
	}

	return new Promise(async (resolve, reject) => {
		// Clean dist folder
		if (fs.existsSync('./dist')) {
			fse.removeSync('./dist');
			info('old dist removed')
		}

		info('Building files...')

		try {
			// Build CSS and JS
			await buildCommand(options, args)
			done('Build files!')
			
			// Dump autoload
			await execa("composer dump-autoload --no-dev --optimize")
			done('Dumped autoload with no dev argument!')

			// Generate translation files
			await translateCommand()
			done('Generated localization strings!')

			// Create the zip file
			createZip()
		} catch(error) {
			reject(error)
		}
	})
}