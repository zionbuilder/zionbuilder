module.exports = {
	root: true,
	env: {
		node: true
	},
	'extends': [
		'plugin:vue/essential',
		'@vue/standard',
		'@vue/typescript',
		// "plugin:compat/recommended"
	],
	rules: {
		'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
		'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
		'indent': ['error', 'tab'],
		'no-tabs': 0
	},
	parserOptions: {
		parser: '@typescript-eslint/parser'
	}
}
