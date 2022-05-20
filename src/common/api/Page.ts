import ZionService from './ZionService';

export function lockPage(id: number) {
	return ZionService.get(`pages/${id}/lock`);
}

export function savePage(pageData) {
	return ZionService.post('save-page', pageData);
}

export function getRenderedContent(id: number) {
	return ZionService.get(`pages/${id}/get_rendered_content`);
}
