import ZionService from './ZionService'
import {
	applyFilters
} from '@zb/hooks'

// Bulk actions
export const bulkActions = function (payload) {
	const bulkActionData = applyFilters('zionbuilder/api/bulk_actions/data', {
		actions: payload,
		post_id: window.ZnPbInitalData.page_id || null
	})

	return ZionService.post('bulk-actions', bulkActionData)
}

// Media
export const getImageIds = function (payload) {
	return ZionService.post('media', payload)
}
