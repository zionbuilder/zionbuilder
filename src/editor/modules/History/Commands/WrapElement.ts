import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class WrapElement extends HistoryCommand {
	static commandID = 'editor/elements/wrap_element';

	doCommand() {
		const { element, wrapperType } = this.data;
		const contentStore = useContentStore();
		const parent = element.parent;
		const newElement = contentStore.registerElement(
			{
				element_type: wrapperType,
			},
			parent,
		);

		newElement.addChild(element);
		parent.replaceChild(element, newElement);

		if (newElement) {
			// const historyManager = this.getHistory();
			// // Add to history
			// historyManager.addHistoryItem({
			// 	undo: WrapElement.undo,
			// 	redo: WrapElement.redo,
			// 	data: {
			// 		elementModel: newElement.toJSON(),
			// 		parentUID,
			// 		index,
			// 	},
			// 	title: newElement.name,
			// 	action: this.getActionName('added'),
			// });
			// return newElement.uid;
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
