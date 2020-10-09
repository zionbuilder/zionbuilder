import Collection from "../Collection";
import PageArea from './PageArea'

export default class PageAreas extends Collection {

	getModel() {
		return PageArea
	}

	get activeArea () {
		return this.find({active: true}) || []
	}
}