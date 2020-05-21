import ZionService from './ZionService'

export const lockPage = function (id) {
	return ZionService.get(`pages/${id}/lock`)
}

export const savePage = function (pageData) {
	return ZionService.post('save-page', pageData)
}
