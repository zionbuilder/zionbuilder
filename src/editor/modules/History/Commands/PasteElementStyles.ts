import { HistoryCommand } from '../HistoryCommand';
import { useContentStore } from '/@/editor/store';
import { merge, set, get, cloneDeep } from 'lodash-es';
import { unref } from 'vue';

export class PasteElementStyles extends HistoryCommand {
	static commandID = 'editor/elements/paste-styles';

	doCommand() {
		const { element, styles } = <
			{ element: ZionElement; styles: { styles: Record<string, unknown>; custom_css: string } }
		>this.data;

		const oldStyles = JSON.parse(
			JSON.stringify({
				styles: element.options._styles || {},
				custom_css: get(element, 'options._advanced_options._custom_css', ''),
			}),
		);

		// Element styles
		// TODO: sa adaug doar stilurile pe care le are si noul element
		if (styles.styles) {
			if (!element.options._styles) {
				element.options._styles = {};
			}

			merge(element.options._styles || {}, styles.styles);
		}

		// Copy custom css
		if (styles.custom_css.length) {
			const existingStyles = get(element, 'options._advanced_options._custom_css', '');
			set(element, 'options._advanced_options._custom_css', existingStyles + styles.custom_css);
		}

		const historyManager = this.getHistory();
		// Add to history
		historyManager.addHistoryItem({
			undo: PasteElementStyles.undo,
			redo: PasteElementStyles.redo,
			data: {
				elementUID: element.uid,
				oldStyles,
				newStyles: styles,
			},
			title: element.name,
			action: this.getActionName('pasteStyles'),
		});
	}

	public static undo(historyItem) {
		const { elementUID, oldStyles } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			element.options._styles = JSON.parse(JSON.stringify(oldStyles.styles));

			if (element.options?._advanced_options?._custom_css && oldStyles.custom_css.length) {
				element.options._advanced_options._custom_css = oldStyles.custom_css;
			}
		}
	}

	public static redo(historyItem) {
		const { elementUID, newStyles } = historyItem.data || {};
		const contentStore = useContentStore();
		const element = contentStore.getElement(elementUID);

		if (element) {
			if (newStyles.styles) {
				if (!element.options._styles) {
					element.options._styles = {};
				}

				merge(element.options._styles || {}, newStyles.styles);
			}

			// Copy custom css
			if (newStyles.custom_css.length) {
				const existingStyles = get(element, 'options._advanced_options._custom_css', '');
				set(element, 'options._advanced_options._custom_css', existingStyles + newStyles.custom_css);
			}
		}
	}
}
