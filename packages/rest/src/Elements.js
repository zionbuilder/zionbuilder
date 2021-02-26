import ZionService from './ZionService'

export const getOptionsForm = function (payload) {
	return ZionService.post('elements/get_element_options_form', {
		element_data: payload
	})
}
export const getElementRender = function (payload) {
	return ZionService.post('elements/render_element', payload)
}
