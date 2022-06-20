import { getService } from './ZionService';

// Bulk actions
export function bulkActions(payload) {
	const bulkActionData = {
		actions: payload,
		post_id: window.ZnPbInitialData ? window.ZnPbInitialData.page_id : null,
	};

	return getService().post('bulk-actions', bulkActionData);
}

// Media
export function getImageIds(payload) {
	return getService().post('media', payload);
}
