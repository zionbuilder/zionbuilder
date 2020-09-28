export default () => {
	const callbacks: Object = {}

	/**
	 * Add an event listener.
	 */
	const on = (event: string, callback) => {
		if (typeof callbacks[event] === 'undefined') {
			callbacks[event] = []
		}

		callbacks[event].push(callback)
	}

	/**
	 * Remove an event listener.
	 */
	const off = (event: string, callback) => {
		if (typeof callbacks[event] !== 'undefined') {
			const callbackIndex = callbacks[event].indexOf(callback)
			if (callbackIndex !== -1) {
				callbacks[event].splice(callbackIndex)
			}
		}
	}

	/**
	 * Dispatch an event.
	 */
	const dispatch = (event: string, detail = {}) => {
		if (typeof callbacks[event] !== 'undefined') {
			callbacks[event].forEach(callbackFunction => {
				callbackFunction({ detail })
			})
		}
	}

	return {
		on,
		off,
		dispatch
	}
}
