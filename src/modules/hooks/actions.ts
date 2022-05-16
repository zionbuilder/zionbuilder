export default () => {
	const actions: Hook = {};

	/**
	 * Add an event listener.
	 */
	const on = (event: string, callback: CallbackFunction) => {
		if (typeof actions[event] === 'undefined') {
			actions[event] = [];
		}

		actions[event].push(callback);
	};

	/**
	 * Remove an event listener.
	 */
	const off = (event: string, callback: CallbackFunction) => {
		if (typeof actions[event] !== 'undefined') {
			const callbackIndex = actions[event].indexOf(callback);
			if (callbackIndex !== -1) {
				actions[event].splice(callbackIndex);
			}
		}
	};

	/**
	 * Dispatch an event.
	 */
	const trigger = (event: string, ...data) => {
		if (typeof actions[event] !== 'undefined') {
			actions[event].forEach(callbackFunction => {
				callbackFunction(...data);
			});
		}
	};

	return {
		on,
		off,
		trigger,
	};
};
