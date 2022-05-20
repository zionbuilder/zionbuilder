import ZionService from './ZionService';

export function replaceUrl(url: string) {
	return ZionService.post('replace-url', url);
}
