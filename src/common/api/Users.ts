import { WPService } from './WPService';

export function getUsers() {
	return WPService.get('users');
}

export function searchUser(options) {
	return WPService.get(`users`, {
		params: {
			search: options,
		},
	});
}

export function getUsersById(ids: number[]) {
	return WPService.get(`users`, {
		params: {
			include: ids,
		},
	});
}

export default WPService;
