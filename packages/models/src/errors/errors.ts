import Collection from "../Collection";
import error from './error'

export default class errors extends Collection {
	getModel() {
		return error
	}

	get Errors() {
		return []
	}
	addNotice(error) {
		this.add(error)
	}
	removeNotice(error) {
		this.remove(error)
	}
}