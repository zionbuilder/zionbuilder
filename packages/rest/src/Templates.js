import ZionService from './ZionService'

export const duplicateTemplate = function (templateID) {
	return ZionService.post('templates/duplicate', {
		template_id: templateID
	})
}

export const updateTemplate = function (templateID, templateData) {
	return ZionService.post(`templates/${templateID}`, templateData)
}

export function saveThumbnailData(id, data) {
	return ZionService.post(`templates/${id}/save-thumbnail`, data)
}
