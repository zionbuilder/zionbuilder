import { DebouncedHistoryCommand } from '../DebouncedHistoryCommand';
import { useContentStore } from '/@/editor/store';

export class RenameElement extends DebouncedHistoryCommand {
	static commandID = 'editor/elements/rename';

	doCommand() {
		const { elementUID, newName } = this.data;
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			// keep a clone of the old name
			const oldName = element.name;
			element.setName(newName);

			this.addToHistory({
				undo: RenameElement.undo,
				redo: RenameElement.redo,
				data: {
					elementUID,
					oldName,
					newName,
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
		const { data = {}, initialChange = {} } = historyItem;
		const { elementUID } = data;
		const { oldName } = initialChange;

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
