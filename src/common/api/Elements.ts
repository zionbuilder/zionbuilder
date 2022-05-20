import ZionService from './ZionService';

export function getOptionsForm(payload) {
	return ZionService.post('elements/get_element_options_form', {
		element_data: payload,
	});
}
