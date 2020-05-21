const wpPot = require('wp-pot');

const textDomain = process.env.npm_package_name;
const filename = `${textDomain}.pot`;
const packageName = process.env.npm_package_project_name || "A Hogash Project"

wpPot({
  destFile: `./build/languages/${filename}`,
  domain: textDomain,
  package: packageName,
  src: 'build/**/*.php'
});