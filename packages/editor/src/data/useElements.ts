import { ref, readonly, Ref } from 'vue'
import { Element } from './models'

// Global template parts store
const elementsRef: Ref<{[key: string]: Element}> = ref({})

export function useElements() {
	const elements = readonly(elementsRef)

	const registerElement = (config, parent: string): Element => {
		const element = new Element(config, parent)

		elementsRef.value[element.uid] = element

		return element
	}

	const getElement = (elementUID: string) => {
		return elementsRef.value[elementUID]
	}

	return {
		elements,
		registerElement,
		getElement
	}
}