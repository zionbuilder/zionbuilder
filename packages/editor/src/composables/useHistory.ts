import { ref, Ref, computed } from 'vue'

const historyItems: Ref = ref([])
const currentHistoryIndex: Ref = ref(-1)

export function useHistory() {

	const canUndo = computed(() => {
		return currentHistoryIndex.value > 0
	})
	const canRedo = computed(() => {
		return currentHistoryIndex.value < historyItems.value.length - 1
	})

	return {
		historyItems,
		currentHistoryIndex,
		canUndo,
		canRedo
	}

}
