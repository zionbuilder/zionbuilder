import { ref } from 'vue'
import { usePanels } from './usePanels'

const element = ref(null)

export function useEditElement() {
	const editElement = (elementInstance) => {
		const { openPanel } = usePanels()

		element.value = elementInstance

		openPanel('PanelElementOptions')

		if (elementInstance) {
			let currentElement = elementInstance.parent
			while (currentElement.parent) {
				currentElement.treeViewItemExpanded = true
				currentElement = currentElement.parent
			}
		}
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