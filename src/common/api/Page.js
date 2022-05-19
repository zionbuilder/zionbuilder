import ZionService from './ZionService';

export const lockPage = function (id) {
	return ZionService.get(`pages/${id}/lock`);
};

export const savePage = function (pageData) {
	return ZionService.post('save-page', pageData);
};

export const getRenderedContent = function (id) {
	return ZionService.get(`pages/${id}/get_rendered_content`);
};
