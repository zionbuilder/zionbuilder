import { each } from "lodash-es";
import Collection from "../Collection";
import PageElement from './PageElement'

export default class PageElements extends Collection {
	getModel() {
		return PageElement
	}

	addChild (element, index = -1) {
		if (typeof element !== PageElement) {
			element = new PageElement(element, this)
		}

		this.models.splice(index, 0, element)
	}

	addChilds (elements, index = -1) {
		elements.forEach(element => {{
			this.addChild(element, index)
		}})
	}
}