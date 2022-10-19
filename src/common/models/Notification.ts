import { useNotificationsStore } from '../store';

export class Notification {
	public title?: string = '';
	public message = '';
	public type = 'info';
	public delayClose = 5000;

	constructor(data: Notification) {
		Object.assign(this, data);
	}

	remove(): void {
		const notificationsStore = useNotificationsStore();
		notificationsStore.remove(this);
	}
}
