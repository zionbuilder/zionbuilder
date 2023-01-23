import { defineStore } from 'pinia';

export const usePageSettingsStore = defineStore('pageSettings', {
	state: () => {
		return {
			settings: {},
		};
	},
	actions: {
		updatePageSettings(newValues: Record<string, unknown>) {
			this.settings = newValues;
		},
		unsetPageSettings() {
			this.settings = {};
		},
	},
});
