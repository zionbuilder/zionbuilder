import ZionService from './ZionService';

export function addLibraryItem(libraryID: string, data) {
	return ZionService.post(`library/${libraryID}`, data);
}

export function exportLibraryItem(libraryID: string, itemID: number) {
	return ZionService.get(`library/${libraryID}/${itemID}/export`, {
		responseType: 'arraybuffer',
	});
}

export function deleteLibraryItem(libraryID: string, itemID: number) {
	return ZionService.delete(`library/${libraryID}/${itemID}`);
}

export function getLibraryItemBuilderConfig(libraryID: string, itemID: number) {
	return ZionService.get(`library/${libraryID}/${itemID}/get-builder-config`);
}

export function importLibraryItem(libraryID: string, file) {
	return ZionService.post(`library/${libraryID}/import`, file, {
		headers: {
			'Content-Type': 'multipart/form-data',
		},
	});
}

export function exportTemplate(data) {
	return ZionService.post(`library/export`, data, {
		responseType: 'arraybuffer',
	});
}

export function saveLibraryItemThumbnail(libraryID: string, itemID: number, data) {
	return ZionService.post(`library/${libraryID}/${itemID}/save-thumbnail`, data);
}
