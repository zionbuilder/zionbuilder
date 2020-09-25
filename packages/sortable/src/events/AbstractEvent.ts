import invariant from 'tiny-invariant'

export default class AbstractEvent {
	static type = 'Event'
	static cancelable = false

	constructor (data) {
		this.cancelled = false
		this.data = data
	}

	isCanceled () {
		return this.cancelled
	}

	cancel () {
		invariant(!this.cancelAble, 'You are trying to cancel an event that cannot be canceled!')

		if (this.isCancelable) {
			this.cancelled = true
		}
	}

	get type () {
		return this.constructor.type
	}

	get isCancelable () {
		return this.constructor.cancelable
	}
}
