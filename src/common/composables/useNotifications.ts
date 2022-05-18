import { ref, Ref } from 'vue';
import { Notification } from '../models/Notification';

const notifications: Ref<Notification[]> = ref([]);

export const useNotifications = () => {
	const add = (data: Notification) => {
		notifications.value.push(new Notification(data));
	};

	const remove = (notification: Notification) => {
		const index = notifications.value.indexOf(notification);
		notifications.value.splice(index, 1);
	};

	return {
		notifications,
		remove,
		add,
	};
};
