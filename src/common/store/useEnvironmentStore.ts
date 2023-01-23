import { defineStore } from 'pinia';

export const useEnvironmentStore = defineStore('environment', {
	state: () => {
		return window.ZBCommonData.environment;
	},
	getters: {},
	actions: {},
});
