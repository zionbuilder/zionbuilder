import { ref, Ref } from 'vue'
import { Element } from './models'

const activeElementMenu: Ref<null | object> = ref(null)

export function useElementMenu () {
	const showElementMenu = (element: Element, selector) => {
		activeElementMenu.value = {
			element,
			selector
		}
	}

	const hideElementMenu = () => {
		activeElementMenu.value = null
	}

	return {
		showElementMenu,
		hideElementMenu,
		activeElementMenu
	}
}