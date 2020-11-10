import { ref, Ref } from 'vue'
import { cloneDeep, merge } from 'lodash-es'
import { Element } from './models/Element'
import { useElements } from './useElements'

const copiedElement: Ref<object> = ref({
	element: null,
	action: null
})

const copiedElementStyles: Ref<null | object> = ref(null)

const focusedElement: Ref<null | Element> = ref(null)


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
		if (copiedElement.value.action === 'copy') {
			element.addChild(copiedElement.value.element.getClone())
		} else if (copiedElement.value.action === 'cut') {
			copiedElement.value.element.isCutted = false
			copiedElement.value.element.move(element)
		}

		copiedElement.value = {}
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

		copiedElementStyles.value = null
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