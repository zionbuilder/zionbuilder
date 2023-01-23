import * as i18n from '@wordpress/i18n';
import { HistoryCommand } from '../HistoryCommand';
import { useUIStore, useContentStore } from '/@/editor/store';

type HistoryItem = {
	data: {
		templateContent: ZionElementConfig[];
		elementUID: string;
		index: number;
		addedElementsUIDs: string[];
	};
};

export class AddTemplate extends HistoryCommand {
	static commandID = 'editor/elements/add-template';

	doCommand() {
		const { templateContent } = <{ templateContent: ZionElementConfig[] }>this.data;
		const UIStore = useUIStore();
		const contentStore = useContentStore();
		const rootElement = contentStore.contentRootElement;
		const { element = rootElement, index = -1 } = UIStore.libraryInsertConfig;

		// If we have an active element we will need to insert the new template inside/outside it
		const addedElements = element.addChildren(templateContent, index);

		// Add to history
		if (addedElements.length) {
			this.getHistory().addHistoryItem({
				undo: AddTemplate.undo,
				redo: AddTemplate.redo,
				data: {
					templateContent,
					elementUID: element.uid,
					index: index,
					addedElementsUIDs: addedElements.map(el => el.uid),
				},
				title: i18n.__( 'Template', 'zionbuilder' ),
				action: i18n.__( 'added', 'zionbuilder' ),
			});
		}
	}

	public static undo(historyItem: HistoryItem) {
		const { addedElementsUIDs = [] } = historyItem.data || {};
		if (addedElementsUIDs.length) {
			const contentStore = useContentStore();
			addedElementsUIDs.forEach(elementUID => {
				contentStore.deleteElement(elementUID);
			});
		}
	}

	public static redo(historyItem: HistoryItem) {
		const { templateContent, elementUID, index } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		// If we have an active element we will need to insert the new template inside/outside it
		element.addChildren(templateContent, index);
	}
}
