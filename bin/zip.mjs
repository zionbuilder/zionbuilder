import zip from 'bestzip';
// import fse from 'fs-extra';
// import exec from 'child_process';
import path from 'path';
// import fs from 'fs';

const zipFiles = [
  'languages',
  'assets',
  'dist',
  'includes',
  'vendor/autoload.php',
  'vendor/composer',
  'vendor/woocommerce',
  'zionbuilder.php',
  'manifest.json',
  'readme.txt',
];

// function buildZip() {
function createZip() {
  const cwd = process.cwd();
  const parentFolder = cwd.split(path.sep).pop();

  // Prepare files
  const filesForCopy = zipFiles;
  const filesForCopyIncludingFolder = filesForCopy.map(source => {
    return `${parentFolder}${path.sep}${source}`;
  });

  zip({
    source: filesForCopyIncludingFolder,
    destination: `${parentFolder}${path.sep}zion-builder.zip`,
    // Set the cwd to parent directory so we can include the folder name in the archive
    cwd: path.join(cwd, '..'),
  })
    .then(function () {
      console.log('all done!');
    })
    .catch(function (err) {
      console.error(err.stack);
      process.exit(1);
    });
}

//   return new Promise(async (resolve, reject) => {
//     console.log('Building files...');

//     try {
//       // Build CSS and JS
//     //   await buildCommand();
//     //   console.log('Build files!');

//     //   // Dump auto-load
//     //   exec('composer dump-autoload --no-dev --optimize');
//     //   console.log('Dumped autoload with no dev argument!');

//     //   // Generate translation files
//     //   await translateCommand();
//     //   console.log('Generated localization strings!');

//       // Create the zip file
//       createZip();
//     } catch (error) {
//       reject(error);
//     }
//   });
// }

createZip();
