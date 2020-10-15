import { ref, Ref } from 'vue'
import { Element } from './models/Element'

const focusedElement: Ref<null | Element> = ref(null)

export function useElementFocus () {
	const focusElement = (element: Element) => {
		focusedElement.value = element
	}

	const unFocusElement = () => {
		focusedElement.value = null
	}

	const isElementFocused = (element: Element) => {
		return element === focusedElement.value
	}

	return {
		focusElement,
		unFocusElement,
		isElementFocused,
		focusedElement
	}
}