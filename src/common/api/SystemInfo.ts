import { getService } from './ZionService';

export function getSystemInfo() {
	return getService().get('system-info');
}
