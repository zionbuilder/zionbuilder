import { ref, Ref, watch } from 'vue'
import { cloneDeep, merge } from 'lodash-es'
import { Element } from './models/Element'
import { useElements } from './useElements'
import { useHistory } from './useHistory'

const copiedElement: Ref<object> = ref({
	element: null,
	action: null
})

const copiedElementStyles: Ref<null | object> = ref(null)
const focusedElement: Ref<null | Element> = ref(null)

// Preserve focused element on history change
const { currentHistoryIndex } = useHistory()
const { getElement } = useElements()
watch(currentHistoryIndex, (newValue) => {
	if (focusedElement.value !== null) {
		focusedElement.value = getElement(focusedElement.value.uid)
	}
})

export function useElementActions() {
	const copyElement = (element: Element, action = 'copy') => {
		copiedElement.value = {
			element,
			action
		}

		if (action === 'cut') {
			element.isCutted = true
		}
	}

	const pasteElement = (element) => {
		let insertElement = element
		let index = -1

		// If the element is not a wrapper, add to parent element
		if (!element.isWrapper || copiedElement.value.element === element) {
			insertElement = element.parent
			index = element.getIndexInParent() + 1
		}

		if (copiedElement.value.action === 'copy') {
			insertElement.addChild(copiedElement.value.element.getClone(), index)
		} else if (copiedElement.value.action === 'cut') {
			if (copiedElement.value.element === element) {
				copiedElement.value.element.isCutted = false
				copiedElement.value = {}
			} else {
				copiedElement.value.element.isCutted = false
				copiedElement.value.element.move(insertElement, index)
			}

			copiedElement.value = {}
		}
	}

	const resetCopiedElement = () => {
		if (copiedElement.value && copiedElement.value.action === 'cut') {
			copiedElement.value.element.isCutted = false
		}
		copiedElement.value = {}
	}

	const copyElementStyles = (element: Element) => {
		copiedElementStyles.value = cloneDeep(element.options._styles)
	}

	const pasteElementStyles = (element) => {
		const styles = copiedElementStyles.value
		if (!element.options._styles) {
			element.options._styles = styles
		} else {
			merge(element.options._styles, styles)
		}
	}

	const focusElement = (element: Element) => {
		if (typeof element === 'string') {
			const { getElement } = useElements()
			element = getElement(element)
		}

		focusedElement.value = element

	}

	const unFocusElement = () => {
		focusedElement.value = null
	}

	const isElementFocused = (element: Element) => {
		return element === focusedElement.value
	}

	return {
		copyElement,
		pasteElement,
		resetCopiedElement,
		copiedElement,
		copyElementStyles,
		pasteElementStyles,
		copiedElementStyles,
		focusElement,
		unFocusElement,
		isElementFocused,
		focusedElement
	}
}