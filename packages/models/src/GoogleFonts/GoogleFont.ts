import Model from '../Model'

export default class GoogleFont extends Model {

	defaults() {
		return {
			category: '',
			family: '',
			subsets: [],
			variants: []
		}
	}

}