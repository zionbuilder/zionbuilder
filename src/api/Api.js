
import ZionService from './ZionService'

// Bulk actions
export const bulkActions = function (payload) {
	return ZionService.post('bulk-actions', {
		actions: payload
	})
}

// Media
export const getImageIds = function (payload) {
	return ZionService.post('media', payload)
}
