import { getService } from './ZionService';

export function lockPage(id: number) {
	return getService().get(`pages/${id}/lock`);
}

export function savePage(pageData) {
	return getService().post('save-page', pageData);
}

export function getRenderedContent(id: number) {
	return getService().get(`pages/${id}/get_rendered_content`);
}
