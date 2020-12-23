import ZionService from './ZionService'

export const getTemplates = function (config = {}) {
	return ZionService.get('templates', config)
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

export const exportTemplate = function (template) {
	return ZionService.post('templates/export', template, {
		responseType: 'arraybuffer'
	})
}

export const insertTemplate = function (template) {
	return ZionService.post('templates/insert', template)
}
export const deleteTemplate = function (id) {
	return ZionService.delete(`templates/${id}`)
}

export const exportTemplateById = function (id) {
	return ZionService.get(`templates/${id}/export`, {
		responseType: 'arraybuffer'
	})
}

export const importTemplateLibrary = function (templateFile) {
	return ZionService.post(
		'templates/import',
		templateFile, {
			headers: {
				'Content-Type': 'multipart/form-data'
			}
		}
	)
}
