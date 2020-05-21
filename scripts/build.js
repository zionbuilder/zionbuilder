const fs = require('fs')
const copy = require('recursive-copy')
const wpPot = require('wp-pot')
const archiver = require('archiver')
const del = require('del')
const semver = require('semver')

const package_name = process.env.npm_package_name

const filesInclude = [
	"assets/*/**",
	"dist/*/**",
	"includes/*/**",
	"languages/*/**",
	"autoload.php",
	"zionbuilder.php",
]

// Convert arguments to object values
var processArguments = {};
process.argv.forEach(function (val, index, array) {
	if( val.indexOf('version=') === 0 ){
		processArguments.version = val.split("=")[1]
	}
})

var newVersion
// Cleanup from old builds
cleanup().then(paths => {
	copyFiles()
})

function copyFiles(){
	// Copy project files
	copy('./', package_name, {
		filter: filesInclude,
		overwrite: true,
	})
	.then(function(results) {

		// Generate translation files
		generateTranslationFile();

		// Change version number
		changeVersion(processArguments.version, archiveFolder);

	})
	.catch(function(error) {
		cleanup();
	});
}

function titleCase(str) {
   var splitStr = str.toLowerCase().split(' ');
   for (var i = 0; i < splitStr.length; i++) {
       // You do not need to check if i is larger than splitStr length, as your for does that for you
       // Assign it back to the array
       splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
   }
   // Directly return the joined string
   return splitStr.join(' ');
}

function generateTranslationFile(){
	// Run wp pot
	const textDomain = process.env.npm_package_name;
	const filename = `${textDomain}.pot`;
	const packageName = titleCase( textDomain.replace("-", " ") );

	wpPot({
	  destFile: `./${package_name}/languages/${filename}`,
	  domain: textDomain,
	  package: packageName,
	  src: `${package_name}/**/*.php`,
	  team: '<hello@hogash.com> Hogash'
	});
}


/**
 *	Change version number for zionbuilder.php
 */
function changeVersion( version, callback ){
	if( typeof version !== "undefined" ){
		let versionFile = `./${package_name}/zionbuilder.php`;
		newVersion = semver.inc(process.env.npm_package_version, version);

		fs.readFile(versionFile, 'utf8', (err, data) => {
			if (err) {
				cleanup();
				throw err;
			}

			let newData = data.replace( /(?<=Version:\s+)(?:(\d+\.(?:\d+\.)*\d+))/, newVersion );
			fs.writeFile(versionFile, newData,'utf8', (err) => {
				if (err) {
					cleanup();
					throw err;
				}

			 	callback();
			});

		});
	}
	else {
		callback();
	}

}

function archiveFolder(){

	var output = fs.createWriteStream('zionbuilder.zip');
	var archive = archiver('zip');

	output.on('close', function () {
	    // Cleanup
		cleanup();
	});

	archive.on('error', function(err){
		cleanup();
		throw err;
	});

	archive.pipe(output);
	archive.glob(`${package_name}/**/*.*`);
	archive.finalize();

}

function cleanup(){
	return del(package_name);

}
