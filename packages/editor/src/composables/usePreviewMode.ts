import { ref, Ref } from 'vue'

const isPreviewMode: Ref<boolean> = ref(false)

export function usePreviewMode () {
	const setPreviewMode = (state) => {
		isPreviewMode.value = state
	}

	return {
		isPreviewMode,
		setPreviewMode
	}
}