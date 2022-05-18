import { useNotifications } from '../composables/useNotifications';

export class Notification {
	public title?: string = '';
	public message = '';
	public type = 'info';
	public delayClose = 5000;

	constructor(data: Notification) {
		Object.assign(this, data);
	}

	remove() {
		const { remove } = useNotifications();

		return remove(this);
	}
}
