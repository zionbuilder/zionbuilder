module.exports = {
	appName: 'zionbuilder',
	zipFiles: [
		'languages',
		'assets',
		'dist',
		'includes',
		'vendor/composer',
		'vendor/woocommerce',
		'zionbuilder.php',
		'manifest.json',
		'Readme.md',
		'readme.txt',
		'vendor/autoload.php'
	],
	l10n: {
		locations: ['zionbuilder.php', 'includes'],
		domain: 'zionbuilder',
		package: 'Zionbuilder',
		bugReport: 'https://github.com/zionbuilder/zionbuilder/issues/new/choose',
		team: 'ZionBuilder <hello@zionbuilder.io>'
	}
}