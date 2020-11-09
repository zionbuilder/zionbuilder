import { useNotifications } from '../useNotifications'

export class Notification {
	public title: string = ''
	public message: string = ''
	public type: string = 'info'

	constructor(data: Notification) {
		Object.assign(this, data)
	}

	remove() {
		const { remove } = useNotifications()

		remove(this)
	}
}