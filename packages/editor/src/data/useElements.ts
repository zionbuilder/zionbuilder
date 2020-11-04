import { reactive } from 'vue'
import { Element } from './models'

// Global template parts store
const elements: {[key: string]: Element} = reactive({})

export function useElements() {
	const registerElement = (config, parent: string): Element => {
		const element = new Element(config, parent)

		elements[element.uid] = element

		return element
	}

	const unregisterElement = (uid: string) => {
		delete elements[uid]
	}

	const getElement = (elementUID: string) => {
		return elements[elementUID]
	}

	return {
		elements,
		registerElement,
		unregisterElement,
		getElement
	}
}