import { Ref, ref } from 'vue'
import { Element } from './models'

// Global template parts store
const elements: Ref<{[key: string]: Element}> = ref({})

export function useElements() {
	const registerElement = (config, parent: string): Element => {
		let element

		// check to see if the element exists
		element = new Element(config, parent)
		elements.value[element.uid] = element

		return element
	}

	const unregisterElement = (uid: string) => {
		delete elements.value[uid]
	}

	const getElement = (elementUID: string) => {
		return elements.value[elementUID]
	}

	return {
		elements,
		registerElement,
		unregisterElement,
		getElement
	}
}