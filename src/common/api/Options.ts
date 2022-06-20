import { getService } from './ZionService';

export function saveOptions(options) {
	return getService().post('options', options);
}

export function getSavedOptions() {
	return getService().get('options');
}
