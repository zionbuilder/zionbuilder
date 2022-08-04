import { defineStore } from 'pinia';
import { debounce } from 'lodash-es';

type HistoryItem = {
	undo: Function;
	redo: Function;
	data: Record<string, unknown>;
	title: string;
	subtitle?: string;
	action?: string;
};

export const useHistoryStore = defineStore('history', {
	state: (): {
		state: HistoryItem[];
		activeHistoryIndex: number;
		isDirty: boolean;
	} => {
		return {
			state: [],
			activeHistoryIndex: -1,
			isDirty: false,
		};
	},
	getters: {
		canUndo: state => {
			return state.activeHistoryIndex > 0;
		},
		canRedo: state => {
			return state.activeHistoryIndex < state.state.length - 1;
		},
	},
	actions: {
		addHistoryItem(item: HistoryItem) {
			this.state.push(item);
			this.activeHistoryIndex++;
		},
		addHistoryItemDebounced: debounce(function (item) {
			this.state.push(item);
			this.activeHistoryIndex++;
		}, 800),
		undo() {
			if (this.activeHistoryIndex - 1 >= 0) {
				const newHistoryIndex = this.activeHistoryIndex - 1;
				// Restore the history
				this.restoreHistoryToIndex(newHistoryIndex);
			}
		},
		redo() {
			const newHistoryIndex = this.activeHistoryIndex + 1;
			if (newHistoryIndex <= this.state.length - 1) {
				this.activeHistoryIndex = newHistoryIndex;
				this.restoreHistoryToIndex(newHistoryIndex);
				this.isDirty = true;
			}
		},
		restoreHistoryToIndex(newHistoryIndex: number) {
			// don't proceed if we are at the same index
			if (newHistoryIndex === this.activeHistoryIndex) {
				return;
			}

			// undo
			if (newHistoryIndex < this.activeHistoryIndex) {
				for (let i = this.state.length - 1; i > newHistoryIndex; i--) {
					const historyItem = this.state[i];
					historyItem.undo(historyItem);
				}
			} else if (newHistoryIndex > this.activeHistoryIndex) {
				const historyForRestore = this.state.slice(this.activeHistoryIndex + 1, newHistoryIndex + 1);
				historyForRestore.forEach(historyItem => {
					historyItem.redo(historyItem);
				});
			}

			// Set the active history index
			this.activeHistoryIndex = newHistoryIndex;

			// Set dirty flag
			this.isDirty = true;
		},
	},
});
