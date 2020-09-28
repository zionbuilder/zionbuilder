class EventBus {
	/**
     * Initialize a new event bus instance.
     */
	constructor () {
		this.callbacks = {}
	}

	/**
     * Add an event listener.
     */
	addEventListener (event, callback) {
		if (typeof this.callbacks[event] === 'undefined') {
			this.callbacks[event] = []
		}

		this.callbacks[event].push(callback)
	}

	/**
     * Remove an event listener.
     */
	removeEventListener (event, callback) {
		if (typeof this.callbacks[event] !== 'undefined') {
			const callbackIndex = this.callbacks[event].indexOf(callback)
			if (callbackIndex !== -1) {
				this.callbacks[event].splice(callbackIndex)
			}
		}
	}

	/**
     * Dispatch an event.
     */
	dispatchEvent (event, detail = {}) {
		if (typeof this.callbacks[event] !== 'undefined') {
			this.callbacks[event].forEach(callbackFunction => {
				callbackFunction({ detail })
			})
		}
	}
}

export default new EventBus()
