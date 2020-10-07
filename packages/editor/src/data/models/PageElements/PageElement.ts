import Model from '../Model'

export default class PageElement extends Model {
	defaults () {
		return {
			element_type: null,
			options: {},
			uid: null,
			content: []
		}
	}

	get name () {
		return this.options.getValue('_advanced_options._element_name')
	}

	set name (name) {
		this.options.setValue('_advanced_options._element_name', name)
	}
}