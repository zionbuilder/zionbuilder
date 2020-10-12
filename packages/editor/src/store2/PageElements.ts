import { each } from 'lodash-es'
import { reactive, watch } from 'vue'
import PageElement from './PageElement'

export default class {
	elements = reactive({})

	constructor() {
		// Create root element
		this.addElement({
			uid: 'contentRoot',
			element_type: 'contentRoot',
			content: []
		})

		watch(this.elements, function(newValue) {
			console.log({...newValue} )
		})
	}

	addElement (elementConfig, parentUID = null) {
		const element = new PageElement(elementConfig, parentUID)

		// Add element to store
		this.elements[element.uid] = element

		return element
	}

	addElements (elementConfigs, parent = null) {
		each(elementConfigs, elementConfig => this.addElement(elementConfig, parent))
	}

	getElement (elementId) {
		return this.elements[elementId]
	}

	deleteElement (elementId) {
		const element = this.getElement(elementId)

		if (element) {
			element.delete()
		}
	}
}