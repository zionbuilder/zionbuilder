export class Elements {
	elements: any[] = []
	categories: any[] = []

	addElements (elements: any) {
		this.elements = {
			...this.elements,
			...elements
		}
	}

	addCategories (categories: any) {
		this.categories = {
			...this.categories,
			...categories
		}
	}

	addCategory = (category: any) => {
		this.categories.push(category)
	}

	getCategories = () => {
		return this.categories
	}

	getElement (elementType: string) {
		const element = this.elements.find((element) => element.element_type === elementType)
		if (element === undefined) {
			// eslint-disable-next-line
			console.warn(`Could not retrieve the element ${elementType}. Did you registered it?`)
			return false
		}

		return element
	}

	registerElement = (element: any) => {
		this.elements[element.name] = element
	}

	getElementsByCategory (categoryId: string) {
		if (categoryId === 'all') {
			return this.elements
		}

		return this.elements.filter(element => {
			return element.category !== undefined && element.category === categoryId
		})
	}

	registerElementComponent = (component: any) => {
		const elementId = component.name
		const element = this.getElement(elementId)

		if (!elementId) {
			return false
		}

		element.component = component
	}

	getElementComponent = (elementId: string) => {
		const element = this.getElement(elementId)

		return element.component || false
	}

	getElements = () => {
		return this.elements
	}
}
