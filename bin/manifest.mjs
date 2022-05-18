import path from 'path';
import fs from 'fs';

export function generateManifest(extraData = {}) {
  const outputFilePath = path.resolve('../', 'manifest.json');
  let data = {
    appName: 'zionbuilder',
    outputDir: 'dist',
    ...extraData,
  };

  fs.writeFile(outputFilePath, JSON.stringify(data), function (err) {
    if (err) return console.error(err);
    console.log('Manifest file written!');
  });
}
