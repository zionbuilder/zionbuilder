import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class SetElementVisibility extends HistoryCommand {
	static commandID = 'editor/elements/set_visibility';

	doCommand() {
		const { elementUID, isVisible } = this.data;
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			// Set the visibility
			element.setVisibility(isVisible);

			const historyManager = this.getHistory();
			// Add to history
			historyManager.addHistoryItem({
				undo: SetElementVisibility.undo,
				redo: SetElementVisibility.redo,
				data: {
					elementUID,
					isVisible,
				},
				title: element.name,
				action: isVisible ? this.getActionName('show') : this.getActionName('hide'),
			});
		}
	}

	public static undo(historyItem) {
		const { elementUID, isVisible } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.setVisibility(!isVisible);
		}
	}

	public static redo(historyItem) {
		const { elementUID, isVisible } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.setVisibility(isVisible);
		}
	}
}
