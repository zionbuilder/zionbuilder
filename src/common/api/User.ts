import { getService } from './ZionService';

export function saveUserData(userData) {
	return getService().post(`user-data`, userData);
}
