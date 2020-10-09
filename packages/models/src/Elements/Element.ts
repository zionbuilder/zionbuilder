import Model from '../Model'

export default class Element extends Model {
	defaults () {
		return {
			element_type: '',
			name: '',
			component: null,
			category: '',
			deprecated: false,
			has_multiple: '',
			icon: '',
			is_child: false,
			keywords: [],
			label: '',
			options: {},
			scripts: {},
			styles: {},
			show_in_ui: true,
			style_elements: {},
			wrapper: false
		}
	}

	getComponent () {
		return this.component
	}
}
