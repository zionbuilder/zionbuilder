import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class RenameElement extends HistoryCommand {
	static commandID = 'editor/elements/rename';

	doCommand() {
		const { elementUID, newName } = this.data;
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			// keep a clone of the old name
			const oldName = element.name;
			element.setName(newName);

			const historyManager = this.getHistory();
			historyManager.addHistoryItemDebounced({
				undo: this.constructor.undo,
				redo: this.constructor.redo,
				data: {
					elementUID: element.uid,
					oldName: oldName,
					newName: newName,
				},
				title: element.name,
				action: this.getActionName('renamed'),
			});
		}
	}

	/**
	 * Undo command. Will re-add the deleted element
	 *
	 * @param historyItem
	 */
	public static undo(historyItem) {
		const { data = {} } = historyItem;
		const { elementUID, oldName } = data;

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.setName(oldName);
		}
	}

	public static redo(historyItem) {
		const { data = {} } = historyItem;
		const { elementUID, newName } = data;

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.setName(newName);
		}
	}
}
