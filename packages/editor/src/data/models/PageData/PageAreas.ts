import Collection from "../Collection";
import PageArea from './PageArea'

export default class PageAreas extends Collection {

	getModel() {
		return PageArea
	}

	get activeAreaContent () {
		const activeArea = this.find({active: true})
		if (activeArea) {
			return activeArea.content.models
		}

		return []
	}
}