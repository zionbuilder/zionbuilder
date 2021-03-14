import babel from '@rollup/plugin-babel';
import replace from '@rollup/plugin-replace';
import bundleSize from '@atomico/rollup-plugin-sizes';
import {
	terser
} from 'rollup-plugin-terser';
import visualizer from 'rollup-plugin-visualizer';
import license from 'rollup-plugin-license';
import compiler from '@ampproject/rollup-plugin-closure-compiler';
import pkg from '../package.json';

const getFileName = (input) => input.split('/')[1].split('.')[0];

const inputs = ['src/index.js'];
const bundles = [{
		inputs,
		format: 'umd',
		dir: 'dist',
		minify: true
	},
	{
		inputs,
		format: 'esm',
		dir: 'dist'
	},
	{
		inputs,
		format: 'cjs',
		dir: 'dist'
	},
];

const configs = bundles
	.map(({
			inputs,
			dir,
			format,
			minify,
			flow
		}) =>
		inputs.map((input) => ({
			input,
			plugins: [
				format === 'umd' &&
				replace({
					__DEV__: minify ? 'false' : 'true',
				}),
				babel({
					babelHelpers: 'bundled'
				}),
				// The two minifiers together seem to procude a smaller bundle 🤷‍♂️
				minify && compiler(),
				minify && terser(),
				license({
					banner: `@zionbuilder/modalJS v${pkg.version} - MIT License`
				}),
				flow && flowEntry({
					types: `lib/${getFileName(input)}.js`
				}),
				bundleSize(),
				visualizer({
					sourcemap: true,
					filename: `stats/${getFileName(input)}${minify ? '-min' : ''}.html`,
				}),
			].filter(Boolean),
			output: {
				name: 'ModalJS',
				file: `${dir}/${format}/${getFileName(input)}${minify ? '.min' : ''	}.js`,
				format,
				sourcemap: true,
			},
		}))
	)
	.flat();

export default configs;
