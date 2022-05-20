import ZionService from './ZionService';

export function saveUserData(userData) {
	return ZionService.post(`user-data`, userData);
}
