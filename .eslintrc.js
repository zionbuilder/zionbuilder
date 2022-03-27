module.exports = {
	root: true,
	env: {
		node: true,
		'vue/setup-compiler-macros': true,
	},
	extends: [
		'plugin:vue/vue3-recommended',
		'eslint:recommended',
		'@vue/eslint-config-typescript/recommended',
		'@vue/eslint-config-prettier',
	],
	rules: {
		'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
		'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
		'no-non-null-assertion': 'off',
		'vue/v-on-event-hyphenation': [
			'error',
			'always',
			{
				ignore: ['update:modelValue'],
			},
		],
		'vue/attribute-hyphenation': [
			'error',
			'always',
			{
				ignore: ['modelValue'],
			},
		],
	},
};
