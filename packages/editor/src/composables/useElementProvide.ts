import { provide, inject } from 'vue'

const ElementSymbol = Symbol()

export const useElementProvide = () => {
	const provideElement = (element) => {
		provide(ElementSymbol, element)
	}

	const injectElement = () => {
		const element = inject(ElementSymbol)
		if (!element) {
		  // throw error, no store provided
		  console.error('No element was provided')
		}
		return element
	}

	return {
		provideElement,
		injectElement
	}
}