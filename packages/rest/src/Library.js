import ZionService from './ZionService'

export function addLibraryItem(librartID, data) {
	return ZionService.post(`library/${librartID}`, data)
}

export function exportLibraryItem(librartID, itemID) {
	return ZionService.get(`library/${librartID}/${itemID}/export`, {
		responseType: 'arraybuffer'
	})
}

export function deleteLibraryItem(librartID, itemID) {
	return ZionService.delete(`library/${librartID}/${itemID}`)
}

export function getLibraryItemBuilderConfig(librartID, itemID) {
	return ZionService.get(`library/${librartID}/${itemID}/get-builder-config`)
}

export function importLibraryItem(librartID, file) {
	return ZionService.post(`library/${librartID}/import`, file, {
		headers: {
			'Content-Type': 'multipart/form-data'
		}
	})
}

export function exportTemplate(data) {
	return ZionService.post(`library/export`, data, {
		responseType: 'arraybuffer'
	})
}
