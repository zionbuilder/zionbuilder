import { createWPService } from './WPService';

export function getUsers() {
	return createWPService().get('users');
}

export function searchUser(options) {
	return createWPService().get(`users`, {
		params: {
			search: options,
		},
	});
}

export function getUsersById(ids: number[]) {
	return createWPService().get(`users`, {
		params: {
			include: ids,
		},
	});
}
