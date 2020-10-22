import { ref, Ref } from 'vue'

const activePopup: Ref<null | object> = ref(null)

export function useAddElementsPopup () {
	const showAddElementsPopup = (element, selector) => {
		console.log(activePopup.value && activePopup.value.element === element);
		if (activePopup.value && activePopup.value.element === element) {
			hideAddElementsPopup()
			return
		}

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