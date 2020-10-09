import { Elements, ElementCategories } from '@zionbuilder/models'

export const initElements = () => {
	return new Elements(window.ZnPbInitalData.elements_data)
}

export const initElementCategories = () => {
	return new ElementCategories(window.ZnPbInitalData.elements_categories)
}