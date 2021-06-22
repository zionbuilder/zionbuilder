import { ref, Ref } from 'vue'
import localSt from 'localstorage-ttl'
import { getLibraryItems } from '@zb/rest'

const cachedData = localSt.get('znpbLibraryCache')
const libraryItems = ref(cachedData ? cachedData.items : [])
const libraryCategories = ref(cachedData ? cachedData.categories : [])

const activeElement: Ref<null | object> = ref(null)

export const useLibrary = () => {
	function fetchLibraryItems() {
		return getLibraryItems().then((response) => {
			const { categories = {}, items = [] } = response.data
			libraryItems.value = items
			libraryCategories.value = categories

			localSt.set('znpbLibraryCache', {
				categories,
				items
			}, 604800000)
		})
	}

	function unsetActiveElementForLibrary() {
		activeElement.value = null
	}


	function setActiveElementForLibrary(element, config = {}) {
		if (activeElement.value && activeElement.value.element === element) {
			return
		}

		activeElement.value = {
			element,
			config
		}
	}

	function getElementForInsert() {
		const { element, config } = activeElement.value
		const { placement = 'inside' } = config

		if (placement === 'inside' && (element.isWrapper || element.element_type === 'contentRoot')) {
			return {
				element
			}
		} else {
			const index = element.getIndexInParent() + 1

			return {
				element: element.parent,
				index
			}
		}
	}

	function insertElement(newElement) {
		const { element, index = -1 } = getElementForInsert()
		newElement = Array.isArray(newElement) ? newElement : [newElement]
		element.addChildren(newElement, index)
	}

	return {
		libraryItems,
		libraryCategories,
		activeElement,
		setActiveElementForLibrary,
		unsetActiveElementForLibrary,
		fetchLibraryItems,
		insertElement
	}
}