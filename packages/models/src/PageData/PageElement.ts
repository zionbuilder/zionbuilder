import Model from '../Model'
import { getOptionValue } from '@zb/utils'
import { each } from 'lodash-es'

export default class PageElement extends Model {
	defaults () {
		return {
			// Element data
			element_type: null,
			options: {},
			uid: null,
			content: [],
			// Helpers
		}
	}

	get elementTypeModel () {
		const elementType = allValues.element_type
		const elementTypeModel = window.zb.editor.elements.getElement(elementType)

		if (elementTypeModel) {
			return elementTypeModel
		}

		return null
	}

	get parent () {

	}

	get isWrapper () {
		return true
		return this.elementTypeModel && this.elementTypeModel.wrapper || false
	}


	get name () {
		// TODO: implement options system
		return getOptionValue(this.options, '_advanced_options._element_name') || 'My Name'
		return this.options.getValue('_advanced_options._element_name')
	}

	get isVisible () {
		return getOptionValue(this.options, '_isVisible', true)
	}

	set isVisible (value) {
		this.options._isVisible = value
	}

	addChild (element, index = -1) {
		if (typeof element !== PageElement) {
			element = new PageElement(element, this)
		}

		this.content.splice(index, 0, element)
	}

	addChilds (elements, index = -1) {
		elements.forEach(element => {{
			this.addChild(element, index)
		}})
	}

}