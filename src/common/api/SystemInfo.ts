import ZionService from './ZionService';

export function getSystemInfo() {
	return ZionService.get('system-info');
}
