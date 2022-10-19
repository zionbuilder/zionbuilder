import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class duplicateElement extends HistoryCommand {
	static commandID = 'editor/elements/duplicate';

	doCommand() {
		const contentStore = useContentStore();
		const { element } = this.data;

		const newElement = contentStore.duplicateElement(element);

		if (newElement) {
			const historyManager = this.getHistory();

			// Add to history
			historyManager.addHistoryItem({
				undo: duplicateElement.undo,
				redo: duplicateElement.redo,
				data: {
					elementModel: newElement.toJSON(),
					elementUID: element.uid,
				},
				title: element.name,
				action: this.getActionName('duplicate'),
			});

			return newElement.uid;
		}

		return null;
	}

	public static undo(historyItem) {
		const { elementModel } = historyItem.data;
		if (elementModel) {
			const contentStore = useContentStore();
			contentStore.deleteElement(elementModel.uid);
		}
	}

	public static redo(historyItem) {
		const { elementUID, elementModel } = historyItem.data;

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			const elementIndex = element.indexInParent;
			contentStore.addElement(elementModel, element.parentUID, elementIndex + 1);
		}
	}
}
