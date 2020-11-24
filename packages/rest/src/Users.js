import WPService from './WPService'

export const getUsers = function (options) {
	return WPService.get('users')
}

export const searchUser = function (options) {
	return WPService.get(`users`, {
		params: {
			search: options
		}
	})
}

export const getUsersById = function (ids) {
	return WPService.get(`users`, {
		params: {
			include: ids
		}
	})
}

export default WPService
