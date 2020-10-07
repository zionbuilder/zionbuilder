import Model from '../Model'
import { getOptionValue } from '@zb/utils'

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
		// TODO: implement options system
		return getOptionValue(this.options, '_advanced_options._element_name') || 'My Name'
		return this.options.getValue('_advanced_options._element_name')
	}

	set name (name) {
		this.options.setValue('_advanced_options._element_name', name)
	}
}