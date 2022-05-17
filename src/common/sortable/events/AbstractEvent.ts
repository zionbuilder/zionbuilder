export default class AbstractEvent {
	static type = 'Event'
	static cancelable = false

	cancelled: boolean
	data: any

	constructor(data: any) {
		this.cancelled = false
		this.data = data
	}

	isCanceled() {
		return this.cancelled
	}

	cancel() {
		if (this.isCancelable) {
			this.cancelled = true
		}
	}

	get type() {
		return AbstractEvent.type
	}

	get isCancelable() {
		return AbstractEvent.cancelable
	}
}
