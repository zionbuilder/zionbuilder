class ElementsFactory {
	constructor () {
		this.registeredElements = []
	}

	registerElement (element) {
		this.registeredElements.push(element)
	}

	registerElements (elements) {
		this.registeredElements = [...this.registeredElements, ...elements]
	}

	unregisterElements (elements) {
		this.registeredElements = this.registeredElements.filter((element) => !elements.includes(element))
	}

	getElementComponent (elementId) {
		return this.registeredElements.find((element) => {
			return element.name === elementId
		})
	}
}

export default new ElementsFactory()
