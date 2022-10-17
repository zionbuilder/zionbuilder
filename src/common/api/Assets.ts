import { getService } from './ZionService';

export type itemData = {
	type: string;
	id?: number;
};

export function regenerateCache(itemData: itemData) {
	return getService().post('assets/regenerate', itemData);
}

export function getCacheList() {
	return getService().get('assets');
}
