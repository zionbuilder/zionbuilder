import { cloneDeep, merge } from 'lodash-es'
import { ref, Ref } from 'vue'

const copiedElementStyles: Ref<null | object> = ref(null)

export function useCopyElementStyles () {
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

	return {
		copyElementStyles,
		pasteElementStyles,
		copiedElementStyles
	}
}