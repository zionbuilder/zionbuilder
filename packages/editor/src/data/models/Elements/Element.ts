import Model from '../Model'

export default class PageElement extends Model {
	defaults () {
		return {
			id: '',
			name: '',
			component: null
		}
	}
}

