import { getService } from './ZionService';

export function getFontsDataSet() {
	return getService().get('data-sets');
}

export function getUserRoles() {
	return getService().get('data-sets/user_roles');
}

export function getIconsList() {
	return getService().get('data-sets/icons');
}
export function deleteIconsPackage() {
	return getService().delete('data-sets/icons');
}
