import { defineStore } from 'pinia';

export const usePageSettingsStore = defineStore('pageSettings', {
	state: () => {
		return {
			settings: {},
		};
	},
	actions: {
		updatePageSettings(newValues) {
			this.settings = newValues;
		},
		unsetPageSettings() {
			this.settings = {};
		},
	},
});
