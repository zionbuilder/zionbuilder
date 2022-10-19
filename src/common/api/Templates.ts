import { getService } from './ZionService';

export function getTemplates(config = {}) {
	return getService().get('templates', {
		params: config,
	});
}

export function addTemplate(template) {
	return getService().post('templates', template);
}

export function duplicateTemplate(templateID: number) {
	return getService().post('templates/duplicate', {
		template_id: templateID,
	});
}

export function updateTemplate(templateID: number, templateData) {
	return getService().post(`templates/${templateID}`, templateData);
}

export function insertTemplate(template) {
	return getService().post('templates/insert', template);
}
export function deleteTemplate(id: number) {
	return getService().delete(`templates/${id}`);
}
