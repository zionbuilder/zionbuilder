import { ref } from 'vue'
import { usePanels } from './usePanels'

const element = ref(null)

export function useEditElement () {
	const editElement = (elementInstance) => {
		const { openPanel } = usePanels()

		element.value = elementInstance
		openPanel('PanelElementOptions')
	}

	const unEditElement = () => {
		const { closePanel } = usePanels()

		element.value = null
		closePanel('PanelElementOptions')
	}

	return {
		element,
		editElement,
		unEditElement
	}
}