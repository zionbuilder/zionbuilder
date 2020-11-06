import Collection from "../Collection";
import Template from './Template'

export default class Templates extends Collection {
	getModel() {
		return Template
	}

	get Templates() {
		return []
	}

	addTemplate(data) {
		this.models.push(data)
	}
}