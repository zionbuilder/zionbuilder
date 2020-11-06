import { ref } from 'vue'

const activeSaveElement = ref({})

export const useSaveTemplate = () => {
	const showSaveElement = (element, type = 'template') => {
		activeSaveElement.value = {
			element,
			type
		}
	}

	const hideSaveElement = () => {
		activeSaveElement.value = {}
	}

	return {
		activeSaveElement,
		showSaveElement,
		hideSaveElement
	}
}