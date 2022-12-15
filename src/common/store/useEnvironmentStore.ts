import { defineStore } from 'pinia';

export const useEnvironmentStore = defineStore('googleFonts', {
	state: () => {
		return window.ZBCommonData.environment;
	},
	getters: {},
	actions: {},
});
