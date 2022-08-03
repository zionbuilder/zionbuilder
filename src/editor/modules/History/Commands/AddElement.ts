import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class AddElement extends HistoryCommand {
	static commandID = 'editor/elements/add';

	doCommand() {
		const contentStore = useContentStore();
		const { element, parentUID, index } = this.data;
		const newElement = contentStore.addElement(element, parentUID, index);

		if (newElement) {
			const historyManager = this.getHistory();
			// Add to history
			historyManager.addHistoryItem({
				undo: this.constructor.undo,
				redo: this.constructor.redo,
				data: {
					elementModel: newElement.toJSON(),
					parentUID,
					index,
				},
				title: newElement.name,
				action: this.getActionName('added'),
			});

			return newElement.uid;
		}

		return null;
	}

	public static undo(historyItem) {
		const { data } = historyItem;
		if (data.elementModel) {
			const contentStore = useContentStore();
			contentStore.deleteElement(data.elementModel.uid);
		}
	}

	public static redo(historyItem) {
		const { data = {} } = historyItem;
		const { elementModel, parentUID, index } = data;

		const contentStore = useContentStore();
		contentStore.addElement(elementModel, parentUID, index);
	}
}
