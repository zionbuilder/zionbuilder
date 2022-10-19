import { getService } from './ZionService';

export function replaceUrl(url: string) {
	return getService().post('replace-url', url);
}
