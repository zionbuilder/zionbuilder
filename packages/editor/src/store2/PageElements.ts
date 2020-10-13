import { each } from 'lodash-es'
import { reactive } from 'vue'
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

	removeElement (element) {
		const uid = element.uid
		const parent = this.elements[element.parent.uid]
		const indexInParent = parent.content.indexOf(uid)

		delete this.elements[uid]
		parent.content.splice(indexInParent, 1)

		// Delete all child elements
		element.content.forEach(uid => {
			const childElement = this.getElement(uid)
			this.removeElement(childElement)
		})
	}

	replaceUIDWithElement (elementContent) {
		return elementContent.map(elementUID => {
			const element = this.getElement(elementUID)

			return element.toJSON()
		})
	}

	toJSON () {
		const rootElement = this.getElement('contentRoot')

		return rootElement.toJSON().content
	}
}