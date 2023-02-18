import { defineStore } from 'pinia';

export const useEnvironmentStore = defineStore('environment', {
	state: () => {
		return window.ZBCommonData.environment;
	},
	getters: {
		isProActive(state): boolean {
			return state.plugin_pro.is_active;
		},
	},
	actions: {},
});
