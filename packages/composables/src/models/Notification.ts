import { useNotifications } from '../useNotifications'

export class Notification {
	public title?: string = ''
	public message: string = ''
	public type: string = 'info'
	public delayClose: number = 5000

	constructor(data: Notification) {
		Object.assign(this, data)
	}

	remove() {
		const { remove } = useNotifications()

		remove(this)
	}
}