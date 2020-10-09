import Model from '../Model'

export default class error extends Model {
	defaults() {
		return {
			title: '',
			message: '',
			type: 'info'
		}
	}

	remove () {
		this.collection.remove(this)
	}
}