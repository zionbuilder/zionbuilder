import { ref } from 'vue'

const pageSettings = ref(window.ZnPbInitalData.page_settings.values)

export const usePageSettings = () => {
	const updatePageSettings = (newValues) => {
		pageSettings.value = newValues
	}

	function unsetPageSettings () {
		pageSettings.value = {}
	}

	return {
		pageSettings,
		updatePageSettings,
		unsetPageSettings
	}
}