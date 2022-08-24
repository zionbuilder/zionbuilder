import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';
import { merge, set, get } from 'lodash-es';

export class PasteElementClasses extends HistoryCommand {
	static commandID = 'editor/elements/paste-css-classes';

	doCommand() {
		const { element, classes } = <{ element: ZionElement; classes: string[] }>this.data;

		const oldCSSClasses = [...get(element.options, '_styles.wrapper.classes', [])];

		merge(element.options, {
			_styles: {
				wrapper: {
					classes,
				},
			},
		});

		const historyManager = this.getHistory();
		// Add to history
		historyManager.addHistoryItem({
			undo: PasteElementClasses.undo,
			redo: PasteElementClasses.redo,
			data: {
				elementUID: element.uid,
				oldCSSClasses,
				newCSSClasses: classes,
			},
			title: element.name,
			action: this.getActionName('pasteCSSClasses'),
		});
	}

	public static undo(historyItem) {
		const { elementUID, oldCSSClasses } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			set(element.options, '_styles.wrapper.classes', [...oldCSSClasses]);
		}
	}

	public static redo(historyItem) {
		const { elementUID, newCSSClasses } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);
		if (element) {
			merge(element.options, {
				_styles: {
					wrapper: {
						classes: [...newCSSClasses],
					},
				},
			});
		}
	}
}
