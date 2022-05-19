import ZionService from './ZionService';

// Bulk actions
export const bulkActions = function (payload) {
	const bulkActionData = {
		actions: payload,
		post_id: window.ZnPbInitalData ? window.ZnPbInitalData.page_id : null,
	};

	return ZionService.post('bulk-actions', bulkActionData);
};

// Media
export const getImageIds = function (payload) {
	return ZionService.post('media', payload);
};
