import * as i18n from '@wordpress/i18n';
import { DebouncedHistoryCommand } from '../DebouncedHistoryCommand';
import { useContentStore } from '/@/editor/store';

export class UpdateElementOptions extends DebouncedHistoryCommand {
	static commandID = 'editor/elements/update-element-options';

	doCommand() {
		const {
			elementUID,
			newValues,
			path = null,
		} = <{ elementUID: string; newValues: Record<string, unknown>; path: string | null }>this.data;
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			// keep a clone of the old name
			const oldValues = JSON.parse(JSON.stringify(element.options));
			element.updateOptionValue(path, newValues);

			this.addToHistory({
				undo: UpdateElementOptions.undo,
				redo: UpdateElementOptions.redo,
				data: {
					elementUID,
					newValues: JSON.parse(JSON.stringify(element.options)),
					oldValues,
					path,
				},
				title: element.name,
				action: i18n.__('Edited', 'zionbuilder'),
			});
		}
	}

	/**
	 * Undo command. Will re-add the deleted element
	 *
	 * @param historyItem
	 */
	public static undo(historyItem) {
		const { data = {}, initialChange = {} } = historyItem;
		const { elementUID } = data;
		const { oldValues } = initialChange;

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);
		if (element) {
			element.updateOptionValue(null, oldValues);
		}
	}

	public static redo(historyItem) {
		const { elementUID, newValues } = historyItem.data || {};

		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.updateOptionValue(null, newValues);
		}
	}
}
