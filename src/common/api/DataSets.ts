import ZionService from './ZionService';

export function getFontsDataSet() {
	return ZionService.get('data-sets');
}

export function getUserRoles() {
	return ZionService.get('data-sets/user_roles');
}

export function getIconsList() {
	return ZionService.get('data-sets/icons');
}
export function deleteIconsPackage() {
	return ZionService.delete('data-sets/icons');
}
