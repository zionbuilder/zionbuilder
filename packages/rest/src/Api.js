
import ZionService from './ZionService'

// Bulk actions
export const bulkActions = function (payload) {
	const postID = window.ZnPbInitalData.page_id || null

	return ZionService.post('bulk-actions', {
		actions: payload,
		post_id: postID
	})
}

// Media
export const getImageIds = function (payload) {
	return ZionService.post('media', payload)
}
