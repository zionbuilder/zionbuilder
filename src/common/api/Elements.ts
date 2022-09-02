import { getService } from './ZionService';

export function getOptionsForm(payload) {
	return getService().post('elements/get_element_options_form', {
		element_data: payload,
	});
}
