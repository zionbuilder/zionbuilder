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
			parent.uid,
		);

		// Get the element model without the children so we can re-add it on redo
		const elementModel = newElement.toJSON();

		newElement.addChild(element);
		parent.replaceChild(element, newElement);

		if (newElement) {
			const historyManager = this.getHistory();
			// Add to history
			historyManager.addHistoryItem({
				undo: WrapElement.undo,
				redo: WrapElement.redo,
				data: {
					elementModel,
					wrappedElementUID: element.uid,
				},
				title: newElement.name,
				action: this.getActionName('wrapped_with_container'),
			});
			return newElement.uid;
		}

		return null;
	}

	public static undo(historyItem) {
		const { elementModel, wrappedElementUID } = historyItem.data;
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementModel.uid);
		const wrappedElement = contentStore.getElement(wrappedElementUID);

		if (element && element.parent && wrappedElement) {
			element.parent.replaceChild(element, wrappedElement);
			contentStore.deleteElement(element.uid);
		}
	}

	public static redo(historyItem) {
		const { elementModel, wrappedElementUID } = historyItem.data;

		const contentStore = useContentStore();
		const element = contentStore.getElement(wrappedElementUID);
		const parent = element?.parent;

		if (element && parent) {
			const newElement = contentStore.registerElement(elementModel, parent.uid);

			newElement.addChild(element);
			parent.replaceChild(element, newElement);
		}
	}
}
