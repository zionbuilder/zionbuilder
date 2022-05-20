import ZionService from './ZionService';

export function saveOptions(options) {
	return ZionService.post('options', options);
}

export function getSavedOptions() {
	return ZionService.get('options');
}
