import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';

export class DiscardElementStyles extends HistoryCommand {
	static commandID = 'editor/elements/discard-element-styles';

	doCommand() {
		const { element } = <{ element: ZionElement }>this.data;

		const oldStyles = JSON.parse(JSON.stringify(element.options._styles || {}));

		delete element.options._styles;

		const historyManager = this.getHistory();
		// Add to history
		historyManager.addHistoryItem({
			undo: DiscardElementStyles.undo,
			redo: DiscardElementStyles.redo,
			data: {
				elementUID: element.uid,
				oldStyles,
			},
			title: element.name,
			action: this.getActionName('discardStyles'),
		});
	}

	public static undo(historyItem) {
		const { elementUID, oldStyles } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.options._styles = JSON.parse(JSON.stringify(oldStyles));
		}
	}

	public static redo(historyItem) {
		const { elementUID } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			delete element.options._styles;
		}
	}
}
