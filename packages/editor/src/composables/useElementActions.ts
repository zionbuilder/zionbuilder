import { ref, Ref, watch } from 'vue'
import { cloneDeep, merge, get, set } from 'lodash-es'
import { Element } from './models/Element'
import { useElements } from './useElements'
import { useHistory } from './useHistory'

const copiedElement: Ref<object> = ref({
	element: null,
	action: null
})

interface ElementCopiedStyles {
	styles: string;
	custom_css: string;
}

const copiedElementStyles: Ref<null | ElementCopiedStyles> = ref(null)
const copiedElementClasses: Ref<null | object> = ref(null)

// Preserve focused element on history change
const { addToHistory } = useHistory()

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
			addToHistory(`Copied ${copiedElement.value.element.name}`)
		} else if (copiedElement.value.action === 'cut') {
			if (copiedElement.value.element === element) {
				copiedElement.value.element.isCutted = false
				copiedElement.value = {}
			} else {
				copiedElement.value.element.isCutted = false
				copiedElement.value.element.move(insertElement, index)
				addToHistory(`Moved ${copiedElement.value.element.name}`)
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
		copiedElementStyles.value = {
			styles: cloneDeep(element.options._styles),
			custom_css: get(element, 'options._advanced_options._custom_css', '')
		}
	}

	const pasteElementStyles = (element) => {
		const styles = copiedElementStyles.value

		if (!styles) {
			return
		}

		// Element styles
		if (styles.styles) {
			if (!element.options._styles) {
				set(element, 'options._styles', styles.styles)
			} else {
				merge(element.options._styles, styles.styles)
			}
		}

		// Copy custom css
		if (styles.custom_css.length) {
			const existingStyles = get(element, 'options._advanced_options._custom_css', '')
			const combinedStyles = existingStyles + styles.custom_css
			set(element, 'options._advanced_options._custom_css', combinedStyles)
		}
	}

	const copyElementClasses = (element: Element) => {
		copiedElementClasses.value = cloneDeep(get(element.options, '_styles.wrapper.classes', null))
	}

	const pasteElementClasses = (element) => {
		merge(element.options, {
			_styles: {
				wrapper: {
					classes: copiedElementClasses.value
				}
			}
		})

	}

	return {
		copyElement,
		pasteElement,
		resetCopiedElement,
		copiedElement,
		copyElementStyles,
		pasteElementStyles,
		copiedElementStyles,
		// Copy element classes
		copiedElementClasses,
		copyElementClasses,
		pasteElementClasses
	}
}