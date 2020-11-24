import invariant from 'tiny-invariant'

export default class AbstractEvent {
	static type = 'Event'
	static cancelable = false

	cancelled: boolean
	data: any

	constructor (data: any) {
		this.cancelled = false
		this.data = data
	}

	isCanceled () {
		return this.cancelled
	}

	cancel () {
		invariant(!AbstractEvent.cancelable, 'You are trying to cancel an event that cannot be canceled!')

		if (this.isCancelable) {
			this.cancelled = true
		}
	}

	get type () {
		return AbstractEvent.type
	}

	get isCancelable () {
		return AbstractEvent.cancelable
	}
}
