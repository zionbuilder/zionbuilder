import { defineStore } from 'pinia';

export const usePageSettingsStore = defineStore('pageSettings', {
	state: () => {
		return {
			settings: window.ZnPbInitalData.page_settings.values,
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
