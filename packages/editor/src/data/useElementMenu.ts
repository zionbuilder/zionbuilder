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

	const showElementMenuFromEvent = (element, event) => {
		showElementMenu(element, {
			ownerDocument: event.view.document,
			getBoundingClientRect () {
				return {
					width: 0,
					height: 0,
					top: event.clientY,
					left: event.clientX
				}

			}
		})
	}

	const hideElementMenu = () => {
		activeElementMenu.value = null
	}

	return {
		showElementMenu,
		showElementMenuFromEvent,
		hideElementMenu,
		activeElementMenu
	}
}