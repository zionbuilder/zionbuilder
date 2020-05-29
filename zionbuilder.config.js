module.exports = {
	appName: 'zionbuilder',
	elementsFolder: './src/elements',
	outputDir: 'dist',
	webpackEntries: {
		'js/editor': './src/editor.js',
		'js/admin-page': './src/admin-page.js',
		'js/edit-page': './src/edit-page.js',
		'js/gutenberg': './src/gutenberg.js',
		'js/preview': './src/preview.js',
		'js/frontend': './src/scss/frontend/frontend.scss',
		'js/video.js': './src/common/vendors/Video.js',
		'js/videoBg.js': './src/common/vendors/videoBg.js'
	},
	css: {
		loaderOptions: {
			sass: {
				prependData: `@import "@/scss/_variables.scss";@import "@/scss/_mixins.scss";`
			}
		}
	},
	zipFiles: [
		'languages',
		'assets',
		'dist',
		'includes',
		'vendor/composer',
		'zionbuilder.php',
		'manifest.json',
		'Readme.md',
		'readme.txt',
		'vendor/autoload.php'
	],
	l10n: {
		locations: ['zionbuilder.php', 'includes'],
		slug: 'zionbuilder',
		packageName: 'Zionbuilder',
		reportBugsUrl: 'https://github.com/zionbuilder/zionbuilder/issues/new/choose',
		team: 'ZionBuilder <hello@zionbuilder.io>'
	},
	configureWebpack: {}
}
