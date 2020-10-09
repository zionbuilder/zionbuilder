import Model from '../Model'

export default class User extends Model {
	defaults() {
		return {
			name: '',
			id: ''
		}
	}
}