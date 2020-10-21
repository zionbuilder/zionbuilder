import { ref, Ref } from 'vue'

const activePopup: Ref<null | object> = ref(null)

export function useAddElementsPopup () {
	const showAddElementsPopup = (element, selector) => {
		activePopup.value = {
			element,
			selector,
			key: Math.random()
		}
	}

	const hideAddElementsPopup = () => {
		activePopup.value = null
	}

	return {
		showAddElementsPopup,
		hideAddElementsPopup,
		activePopup
	}
}