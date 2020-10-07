import Collection from "../Collection";
import Element from './Element'

export default class Elements extends Collection {
	getModel() {
		return Element
	}

	registerElement (elementComponent) {
		const elementType = elementComponent.name
		const element = this.getElement(elementType)

		if (!element) {
			console.warn(`Element with id: ${elementType} was not found. Make sure to also register it in PHP!`)
		} else {
			element.component = elementComponent
		}
	}

	getElement(elementType: string) {
		return this.find({element_type: elementType})
	}
}