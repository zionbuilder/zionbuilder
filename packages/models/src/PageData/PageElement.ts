import Model from '../Model'
import { getOptionValue } from '@zb/utils'
import { each } from 'lodash-es'

export default class PageElement extends Model {
	defaults() {
		return {
			element_type: null,
			options: {},
			uid: null,
			content: [],
			elementTypeModel: null
		}
	}

	mutations() {
		return {
			elementTypeModel: (value, allValues) => {
				const elementType = allValues.element_type
				const elementTypeModel = window.zb.editor.elements.getElement(elementType)

				if (elementTypeModel) {
					return elementTypeModel
				}

				return value
			},
			// content: (value) => {
			// 	if (Array.isArray(value)) {
			// 		const content = []
			// 		each(value, (child, childUid) => {
			// 			console.log(child);

			// 			content.push( new PageElement(child) )
			// 		})

			// 		return content
			// 	}

			// 	return value
			// }
		}
	}

	get name() {
		// TODO: implement options system
		return getOptionValue(this.options, '_advanced_options._element_name') || 'My Name'
		return this.options.getValue('_advanced_options._element_name')
	}

	get isVisible() {
		return getOptionValue(this.options, '_isVisible', true)
	}

	set isVisible(value) {
		this.options._isVisible = value
	}

	get isWrapper() {
		return this.elementTypeModel.wrapper
	}

	addChild(element, index = -1) {
		this.content.addChild(element, index)
	}

	addChildren(elements, index = -1) {
		this.content.addChildren(elements, index)
	}

}