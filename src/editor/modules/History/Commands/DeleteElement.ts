import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class DeleteElement extends HistoryCommand {
	static commandID = 'editor/elements/delete';

	doCommand() {
		const { elementUID } = this.data;
		const contentStore = useContentStore();
		const deletedElement = contentStore.getElement(elementUID);

		if (deletedElement) {
			const historyManager = this.getHistory();

			// Add to history
			historyManager.addHistoryItem({
				undo: DeleteElement.undo,
				redo: DeleteElement.redo,
				data: {
					elementModel: deletedElement.toJSON(),
					parentUID: deletedElement.parentUID,
					index: deletedElement.indexInParent,
				},
				title: deletedElement.name,
				action: this.getActionName('deleted'),
			});

			contentStore.deleteElement(elementUID);
		}
	}

	/**
	 * Undo command. Will re-add the deleted element
	 *
	 * @param historyItem
	 */
	public static undo(historyItem) {
		const { data = {} } = historyItem;
		const { elementModel, parentUID, index } = data;

		const contentStore = useContentStore();
		contentStore.addElement(elementModel, parentUID, index);
	}

	public static redo(historyItem) {
		const { data } = historyItem;
		if (data.elementModel) {
			const contentStore = useContentStore();
			contentStore.deleteElement(data.elementModel.uid);
		}
	}
}
