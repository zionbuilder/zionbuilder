export default () => {
	let handled = false;

	const handle = () => {
		handled = true;
	};

	const reset = () => {
		handled = false;
	};

	return {
		handle,
		reset,
		get isHandled() {
			return handled;
		},
	};
};
