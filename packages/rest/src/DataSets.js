import ZionService from './ZionService'

export const getFontsDataSet = function () {
	return ZionService.get('data-sets')
}
export const getUserRoles = function () {
	return ZionService.get('data-sets/user_roles')
}
export const getIconsList = function () {
	return ZionService.get('data-sets/icons')
}
export const deleteIconsPackage = function () {
	return ZionService.delete('data-sets/icons')
}
