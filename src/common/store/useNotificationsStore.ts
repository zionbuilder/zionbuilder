import { defineStore } from 'pinia';
import { Notification } from '../models/Notification';

interface NotificationStore {
	notifications: Notification[];
}

export const useNotificationsStore = defineStore('notifications', {
	state: (): NotificationStore => {
		return {
			notifications: [],
		};
	},
	actions: {
		add(data: Notification) {
			this.notifications.push(new Notification(data));
		},

		remove(notification: Notification) {
			const index = this.notifications.indexOf(notification);
			this.notifications.splice(index, 1);
		},
	},
});
