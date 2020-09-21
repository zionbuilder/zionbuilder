import Options from './Options'
import { getElement } from '@zb/editor/elements'
import { generateUID } from '@zb/utils'

export default class {
	constructor (elementConfig) {
		const elementData = {
			options: {},
			content: [],
			...elementConfig
		}

		// Set the element Model
		this.elementTypeModel = getElement(elementConfig.element_type)
		this.content = elementData.content
		this.uid = elementData.uid || generateUID()
		this.element_type = elementConfig.element_type
		this.options = new Options(elementConfig.element_type.options, elementData.options)
	}

	getElementModel () {
		return this.elementTypeModel
	}

	getDataForDB () {
		return {
			options: this.options.getValuesForDb(),
			uid: this.uid,
			content: this.content,
			element_type: this.element_type
		}
	}

	getCssSelector = () => {
		return `#${this.uid}`
	}

	getName () {
		return this.options.getValue('_advanced_options._element_name') || this.elementTypeModel.name
	}

	rename (name) {
		this.options.updateValue('_advanced_options._element_name', name)
	}

	addChild (childUid) {

	}

	getComponent = () => {

	}
}
