export default () => {
	const actions: Object = {}

	/**
	 * Add an event listener.
	 */
	const on = (event: string, callback) => {
		if (typeof actions[event] === 'undefined') {
			actions[event] = []
		}

		actions[event].push(callback)
	}

	/**
	 * Remove an event listener.
	 */
	const off = (event: string, callback) => {
		if (typeof actions[event] !== 'undefined') {
			const callbackIndex = actions[event].indexOf(callback)
			if (callbackIndex !== -1) {
				actions[event].splice(callbackIndex)
			}
		}
	}

	/**
	 * Dispatch an event.
	 */
	const trigger = (event: string, detail = {}) => {
		if (typeof actions[event] !== 'undefined') {
			actions[event].forEach(callbackFunction => {
				callbackFunction({ detail })
			})
		}
	}

	return {
		on,
		off,
		trigger
	}
}
