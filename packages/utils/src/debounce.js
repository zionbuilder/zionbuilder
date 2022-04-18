export default function (callback, time = 1000) {
	let timeout;
	return function (...args) {
		const context = this;
		clearTimeout(timeout);
		timeout = setTimeout(() => {
			callback.apply(context, args);
		}, time);
	};
}
