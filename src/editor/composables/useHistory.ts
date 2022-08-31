import { ref, Ref } from 'vue';

const historyItems: Ref = ref([]);
const currentHistoryIndex: Ref = ref(-1);
const isDirty: Ref<boolean> = ref(false);

export function useHistory() {
	function addToHistory(name: string, ignoreDirty = false) {
		// Get the state
		const state = getDataForSave();
		const currentTime = new Date();

		if (!state) {
			return;
		}

		const historyData = {
			state,
			name,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}`,
		};

		currentHistoryIndex.value += 1;

		// If this is a new change tree, remove the old one
		if (currentHistoryIndex.value !== historyItems.value.length) {
			const itemsToRemove = historyItems.value.length - currentHistoryIndex.value;
			historyItems.value.splice(currentHistoryIndex.value, itemsToRemove, historyData);
		} else {
			historyItems.value.push(historyData);
		}
	}

	return {
		historyItems,
		currentHistoryIndex,
		addToHistory,
		isDirty,
	};
}
