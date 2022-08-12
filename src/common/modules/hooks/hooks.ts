export default () => {
	const filters: Hook = {};
	const actions: Hook = {};

	/**
	 * Add an event listener.
	 */
	const addAction = (event: string, callback: CallbackFunction) => {
		if (typeof actions[event] === 'undefined') {
			actions[event] = [];
		}

		actions[event].push(callback);
	};

	function on(event: string, callback: CallbackFunction) {
		console.warn('zb.hooks.on was deprecated in favour of window.zb.addAction');
		return addAction(event, callback);
	}

	/**
	 * Remove an event listener.
	 */
	const removeAction = (event: string, callback: CallbackFunction) => {
		if (typeof actions[event] !== 'undefined') {
			const callbackIndex = actions[event].indexOf(callback);
			if (callbackIndex !== -1) {
				actions[event].splice(callbackIndex);
			}
		}
	};

	function off(event: string, callback: CallbackFunction) {
		console.warn('zb.hooks.off was deprecated in favour of window.zb.addAction');
		return addAction(event, callback);
	}

	/**
	 * Dispatch an event.
	 */
	const doAction = (event: string, ...data) => {
		if (typeof actions[event] !== 'undefined') {
			actions[event].forEach(callbackFunction => {
				callbackFunction(...data);
			});
		}
	};

	const addFilter = (id: string, callback: CallbackFunction) => {
		if (typeof filters[id] === 'undefined') {
			filters[id] = [];
		}

		filters[id].push(callback);
	};

	const applyFilters = (id: string, value: any, ...params: any[]) => {
		if (typeof filters[id] !== 'undefined') {
			filters[id].forEach(callback => {
				value = callback(value, ...params);
			});
		}

		return value;
	};

	return {
		addAction,
		removeAction,
		doAction,
		addFilter,
		applyFilters,

		// Deprecated
		on,
		off,
	};
};
