import Collection from "../Collection";
import PageElement from './PageElement'

export default class PageAreas extends Collection {

	getModel () {
		return PageElement
	}

	get activeArea () {
		return this.find({active: true}) || []
	}
}