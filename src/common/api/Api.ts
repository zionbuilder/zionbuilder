import ZionService from './ZionService';

// Bulk actions
export function bulkActions(payload) {
	const bulkActionData = {
		actions: payload,
		post_id: window.ZnPbInitalData ? window.ZnPbInitalData.page_id : null,
	};

	return ZionService.post('bulk-actions', bulkActionData);
}

// Media
export function getImageIds(payload) {
	return ZionService.post('media', payload);
}
