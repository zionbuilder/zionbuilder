import ZionService from './ZionService';

export function getTemplates(config = {}) {
	return ZionService.get('templates', {
		params: config,
	});
}

export function addTemplate(template) {
	return ZionService.post('templates', template);
}

export function duplicateTemplate(templateID: number) {
	return ZionService.post('templates/duplicate', {
		template_id: templateID,
	});
}

export function updateTemplate(templateID: number, templateData) {
	return ZionService.post(`templates/${templateID}`, templateData);
}

export function insertTemplate(template) {
	return ZionService.post('templates/insert', template);
}
export function deleteTemplate(id: number) {
	return ZionService.delete(`templates/${id}`);
}
