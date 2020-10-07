import Collection from "../Collection";
import ElementCategory from './ElementCategory'

export default class ElementCategories extends Collection {
	getModel() {
		return ElementCategory
	}
}