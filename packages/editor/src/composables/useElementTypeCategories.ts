import { find } from 'lodash-es'
import { ref, Ref } from 'vue'

const categories: Ref = ref(window.ZnPbInitalData.elements_categories)

export function useElementTypeCategories() {
	const addElementTypeCategory = (config) => {
		categories.value.push(config)
	}

	const getElementTypeCategory = (id: string) => {
		return find(categories.value, {id})
	}

	return {
		categories,
		addElementTypeCategory,
		getElementTypeCategory
	}
}