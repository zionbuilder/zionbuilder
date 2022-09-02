import { getService } from './ZionService';

export function regenerateCache() {
	return getService().get('regenerate-cache');
}
