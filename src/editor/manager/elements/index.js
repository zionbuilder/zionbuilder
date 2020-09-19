class ElementsFactory {
	constructor () {
		this.registeredElements = {}
	}

	registerElement = (element) => {
		this.registeredElements[element.name] = element
	}

	getElementComponent = (elementId) => {
		if (typeof this.registeredElements[elementId] === 'undefined') {
			console.error(`Element with id ${elementId} not found. Did you registered it?`)
			return false
		}

		return this.registeredElements[elementId]
	}
}

export default new ElementsFactory()
