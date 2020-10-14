import { ref, readonly, Ref } from 'vue'

const activePopupRef: Ref<null | object> = ref(null)

export function useAddElementsPopup () {
	const activePopup = readonly(activePopupRef)

	const showAddElementsPopup = (element, selector) => {
		activePopupRef.value = {
			element,
			selector
		}
	}

	const hideAddElementsPopup = () => {
		activePopupRef.value = null
	}

	return {
		showAddElementsPopup,
		hideAddElementsPopup,
		activePopup
	}
}