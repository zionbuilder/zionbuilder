import ZionService from './ZionService'

export const saveUserData = function (userData) {
	return ZionService.post(`user-data`, userData)
}
