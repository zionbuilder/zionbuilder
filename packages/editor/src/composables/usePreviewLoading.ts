import { ref, Ref } from 'vue'

const isPreviewLoading: Ref<boolean> = ref(true)

export function usePreviewLoading () {
	const setPreviewLoading = (state: boolean) => {
		isPreviewLoading.value = state
	}

	return {
		setPreviewLoading,
		isPreviewLoading
	}
}