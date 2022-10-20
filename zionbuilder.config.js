module.exports = {
	appName: 'zionbuilder',
	zipFiles: [
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
	],
	l10n: {
		locations: ['zionbuilder.php', 'includes'],
		domain: 'zionbuilder',
		package: 'Zionbuilder',
		bugReport: 'https://feedback.zionbuilder.io',
		team: 'ZionBuilder <hello@zionbuilder.io>',
	},
};
