import { getService } from './ZionService';

export function getOptionsForm(payload: ZionElementConfig) {
	return getService().post('elements/get_element_options_form', {
		element_data: payload,
	});
}
