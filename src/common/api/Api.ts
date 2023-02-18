import { getService } from './ZionService';

// Bulk actions
export function bulkActions(payload: Record<string, unknown>) {
	const bulkActionData = {
		actions: payload,
		post_id: window.ZnPbInitialData ? window.ZnPbInitialData.page_id : null,
	};

	return getService().post('bulk-actions', bulkActionData);
}

// Media
export function getImageIds(payload: { images: string[] }) {
	return getService().post('media', payload);
}
