import Collection from "../Collection";
import Error from './Error'

export default class Errors extends Collection {
	getModel() {
		return Error
	}

	get Errors() {
		return []
	}
}