import { debounce } from 'lodash';
import { HistoryCommand } from './HistoryCommand';
import { useHistoryStore } from '/@/editor/store';

let items = [];

export class DebouncedHistoryCommand extends HistoryCommand {
	static debounce = null;

	constructor(data: Record<string, unknown>) {
		super(data);

		if (this.constructor.debounce === null) {
			this.constructor.debounce = debounce((fn, ...args) => {
				fn(...args);
			}, 800);
		}
	}

	addToHistory(historyItem) {
		items.push(historyItem);
		this.constructor.debounce(this.getHistoryItem, historyItem);
	}

	getHistoryItem(historyItem) {
		const historyStore = useHistoryStore();

		// Set the initial change so we can access it from undo/redo methods
		historyItem.initialChange = items[0].data;
		historyStore.addHistoryItem(historyItem);

		// Clear cache
		items = [];
	}
}
