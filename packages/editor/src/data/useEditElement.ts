import { ref } from 'vue'
import { usePanels } from './usePanels'

const element = ref(null)

export function useEditElement () {
	const editElement = (elementInstance) => {
		const { openPanel } = usePanels()

		element.value = elementInstance
		openPanel('PanelElementOptions')
	}

	return {
		element,
		editElement
	}
}