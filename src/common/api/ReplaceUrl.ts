import { getService } from './ZionService';

export function replaceUrl(urls: { find: string; replace: string }) {
	return getService().post('replace-url', urls);
}
