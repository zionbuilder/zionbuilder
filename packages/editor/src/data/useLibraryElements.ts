import { ref, Ref } from 'vue'

const elementInsertConfig: Ref<null | object> = ref(null)

export function useLibraryElements() {
	const setElementConfigForLibrary = (value: Object) => {
		elementInsertConfig.value = value
	}

	return {
		setElementConfigForLibrary,
		elementInsertConfig
	}
}