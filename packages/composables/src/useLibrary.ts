import { ref } from 'vue'
import localSt from 'localstorage-ttl'
import { getLibraryItems } from '@zb/rest'

const cachedData = localSt.get('znpbLibraryCache')
const libraryItems = ref(cachedData.items || [])
const libraryCategories = ref(cachedData.categories || [])
const activeElementForLibrary = ref(null)

export const useLibrary = () => {
	function fetchLibraryItems () {
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

	function setActiveElementForLibrary(element) {
		activeElementForLibrary.value = element
	}

	function unsetActiveElementForLibrary() {
		activeElementForLibrary.value = null
	}

	return {
		libraryItems,
		libraryCategories,
		activeElementForLibrary,
		setActiveElementForLibrary,
		unsetActiveElementForLibrary,
		fetchLibraryItems
	}
}