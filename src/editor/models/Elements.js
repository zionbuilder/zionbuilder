export default class {
	elements = []

	constructor (elements, categories) {
		this.elements = elements
		this.categories = categories
	}

	addCategory = (category) => {
		this.categories.push(category)
	}

	getCategories = () => {
		return this.categories
	}

	getElement = (elementType) => {
		const element = this.elements.find((element) => element.element_type === elementType)
		if (element === undefined) {
			// eslint-disable-next-line
			console.warn(`Could not retrieve the element ${elementType}. Did you registered it?`)
			return false
		}

		return element
	}

	registerElement = (element) => {
		this.elements[element.name] = element
	}

	getElementsByCategory (categoryId) {
		if (categoryId === 'all') {
			return this.elements
		}

		return this.elements.filter(element => {
			return element.category !== undefined && element.category === categoryId
		})
	}

	registerElementComponent = (component) => {
		const elementId = component.name
		const element = this.getElement(elementId)

		if (!elementId) {
			return false
		}

		element.component = component
	}

	getElementComponent = (elementId) => {
		const element = this.getElement(elementId)

		return element.component || false
	}

	getElements = () => {
		return this.elements
	}
}
