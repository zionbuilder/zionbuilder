import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class MoveElement extends HistoryCommand {
	static commandID = 'editor/elements/move';

	doCommand() {
		const { element, newParent, index } = <
			{
				element: ZionElement;
				newParent: ZionElement;
				index: number;
			}
		>this.data;

		const oldParentUID = element.parentUID;
		const oldIndex = element.indexInParent;

		element.move(newParent, index);

		const historyManager = this.getHistory();
		// Add to history
		historyManager.addHistoryItem({
			undo: MoveElement.undo,
			redo: MoveElement.redo,
			data: {
				elementUID: element.uid,
				oldParentUID,
				newParentUID: newParent.uid,
				newIndex: index,
				oldIndex,
			},
			title: element.name,
			action: this.getActionName('moved'),
		});
	}

	public static undo(historyItem) {
		const { elementUID, oldParentUID, oldIndex } = historyItem.data || {};

		const contentStore = useContentStore();
		const movedElement = contentStore.getElement(elementUID);
		const oldParent = contentStore.getElement(oldParentUID);

		if (movedElement && oldParent) {
			movedElement.move(oldParent, oldIndex);
		}
	}

	public static redo(historyItem) {
		const { elementUID, newParentUID, newIndex } = historyItem.data || {};

		const contentStore = useContentStore();
		const movedElement = contentStore.getElement(elementUID);
		const oldParent = contentStore.getElement(newParentUID);

		if (movedElement && oldParent) {
			movedElement.move(oldParent, newIndex);
		}
	}
}
