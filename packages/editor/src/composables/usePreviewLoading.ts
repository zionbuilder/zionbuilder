import { ref, Ref } from 'vue'

const isPreviewLoading: Ref<boolean> = ref(true)
const loadTimestamp = ref(null)

export function usePreviewLoading() {
	const setPreviewLoading = (state: boolean) => {
		isPreviewLoading.value = state
	}

	function setLoadTimestamp() {
		loadTimestamp.value = Date.now()
	}

	return {
		setPreviewLoading,
		isPreviewLoading,
		loadTimestamp,
		setLoadTimestamp
	}
}