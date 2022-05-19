import { watch } from 'vue';
import { useHistory } from './useHistory';
import { useSavePage } from './useSavePage';

export function useAutosave() {
	let canAutosave = false;
	const { currentHistoryIndex } = useHistory();
	const { saveAutosave } = useSavePage();

	watch(currentHistoryIndex, newValue => {
		if (canAutosave && newValue > 0) {
			saveAutosave();
			canAutosave = false;

			setTimeout(() => {
				canAutosave = true;
			}, window.ZnPbInitalData.autosaveInterval * 1000);
		}
	});
}
