import { reactive } from 'vue'

export const initAddElementsPopup = () => {
	let activePopup = reactive({})

	const show = (element, selector) => {
		activePopup.element = element
		activePopup.selector = selector
		activePopup.active = true
	}

	const hide = () => {
		activePopup.active = false
	}

	return {
		show,
		hide,
		activePopup
	}
}