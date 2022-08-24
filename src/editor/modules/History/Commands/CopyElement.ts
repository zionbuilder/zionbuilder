import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';
import { regenerateUIDs } from '/@/editor/utils';

export class CopyElement extends HistoryCommand {
	static commandID = 'editor/elements/copy';

	doCommand() {
		const { parent, copiedElement, index } = this.data;
		const newElement = parent.addChild(regenerateUIDs(copiedElement), index);

		if (newElement) {
			const historyManager = this.getHistory();
			// Add to history
			historyManager.addHistoryItem({
				undo: CopyElement.undo,
				redo: CopyElement.redo,
				data: {
					elementModel: newElement.toJSON(),
					parentUID: parent.uid,
					index,
				},
				title: newElement.name,
				action: this.getActionName('copied'),
			});

			return newElement.uid;
		}

		return null;
	}

	public static undo(historyItem) {
		const { elementModel } = historyItem.data || {};
		if (elementModel) {
			const contentStore = useContentStore();
			contentStore.deleteElement(elementModel.uid);
		}
	}

	public static redo(historyItem) {
		const { elementModel, parentUID, index } = historyItem.data || {};

		const contentStore = useContentStore();
		contentStore.addElement(elementModel, parentUID, index);
	}
}
