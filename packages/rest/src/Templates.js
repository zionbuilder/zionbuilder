import ZionService from './ZionService'

export const getTemplates = function (config = {}) {
	return ZionService.get('templates', {
		params: config
	})
}

export const addTemplate = function (template) {
	return ZionService.post('templates', template)
}

export const duplicateTemplate = function (templateID) {
	return ZionService.post('templates/duplicate', {
		template_id: templateID
	})
}

export const updateTemplate = function (templateID, templateData) {
	return ZionService.post(`templates/${templateID}`, templateData)
}

export const insertTemplate = function (template) {
	return ZionService.post('templates/insert', template)
}
export const deleteTemplate = function (id) {
	return ZionService.delete(`templates/${id}`)
}
