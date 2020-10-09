import Model from '../Model'
import { getOptionValue } from '@zb/utils'

export default class PageElement extends Model {
	defaults () {
		return {
			element_type: null,
			options: {},
			uid: null,
			content: [],
			elementTypeModel: null
		}
	}

	mutations () {
		return {
			elementTypeModel: (value, allValues) => {
				const elementType = allValues.element_type
				const elementTypeModel = window.zb.editor.elements.getElement(elementType)

				if (elementTypeModel) {
					return elementTypeModel
				}

				return value
			}
		}
	}

	get name () {
		// TODO: implement options system
		return getOptionValue(this.options, '_advanced_options._element_name') || 'My Name'
		return this.options.getValue('_advanced_options._element_name')
	}

	set name (name) {
		this.options.setValue('_advanced_options._element_name', name)
	}

	get isVisible () {
		return getOptionValue(this.options, '_isVisible', true)
	}

	set isVisible (value) {
		this.options._isVisible = value
	}
}