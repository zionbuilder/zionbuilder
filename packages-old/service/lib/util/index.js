const chalk = require('chalk');

const chalkTag = msg => chalk.bgBlackBright.white.dim(` ${msg} `);

const format = (label, msg) => {
	return msg
		.split('\n')
		.map((line, i) => {
			return i === 0 ? `${label} ${line}` : line.padStart(stripAnsi(label).length);
		})
		.join('\n');
};

exports.log = function (msg = '', tag = null) {
	tag ? console.log(format(chalkTag(tag), msg)) : console.log(msg);
};

exports.info = function (msg, tag = null) {
	console.log(format(chalk.bgBlue.black(' INFO ') + (tag ? chalkTag(tag) : ''), msg));
};

exports.done = (msg, tag = null) => {
	console.log(format(chalk.bgGreen.black(' DONE ') + (tag ? chalkTag(tag) : ''), msg));
};

exports.warn = (msg, tag = null) => {
	console.warn(format(chalk.bgYellow.black(' WARN ') + (tag ? chalkTag(tag) : ''), chalk.yellow(msg)));
};

exports.error = (msg, tag = null) => {
	console.error(format(chalk.bgRed(' ERROR ') + (tag ? chalkTag(tag) : ''), chalk.red(msg)));

	if (msg instanceof Error) {
		console.error(msg.stack);
	}
};
