import { ref, Ref } from 'vue'

const copiedElement: Ref<object> = ref({
	element: null,
	action: null
})

export function useCopyCutPasteElement () {
	const copyElement = (element: Element, action = 'copy') => {
		copiedElement.value = {
			element,
			action
		}
	}

	const pasteElement = (element) => {
		if (copiedElement.value.action === 'copy') {
			element.addChild(copiedElement.value.element.getClone())
		} else if (copiedElement.value.action === 'cut') {
			copiedElement.value.element.move(element)
		}


		copiedElement.value = {}
	}

	const resetCopiedElement = () => {
		copiedElement.value = {}
	}

	return {
		copyElement,
		pasteElement,
		resetCopiedElement,
		copiedElement
	}
}