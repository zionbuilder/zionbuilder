import * as i18n from '@wordpress/i18n';
import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';
import { regenerateUIDsForContent } from '/@/editor/utils';

type HistoryItem = {
	data: {
		elements: ZionElementConfig[];
		elementUID: string;
		index: number;
		addedElementsUIDs: string[];
	};
};

export class AddElements extends HistoryCommand {
	static commandID = 'editor/elements/add-elements';

	doCommand() {
		const { elements, elementUID, index } = <{ elements: ZionElementConfig[]; elementUID: string; index: number }>(
			this.data
		);

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		// Generate UIDs so we can re-add the elements and keep the UIDs
		const elementsForInsert = regenerateUIDsForContent(elements);

		if (!element) {
			console.log(`Element with id ${elementUID} not found. Cannot add elements`);
			return;
		}

		// If we have an active element we will need to insert the new template inside/outside it
		const addedElements = element.addChildren(elements, index);

		// Add to history
		if (addedElements.length) {
			this.getHistory().addHistoryItem({
				undo: AddElements.undo,
				redo: AddElements.redo,
				data: {
					elements: elementsForInsert,
					elementUID,
					index,
					addedElementsUIDs: addedElements.map(el => el.uid),
				},
				title: i18n.__( 'Layout', 'zionbuilder' ),
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
		const { elements, elementUID, index } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		// If we have an active element we will need to insert the new template inside/outside it
		element.addChildren(elements, index);
	}
}
