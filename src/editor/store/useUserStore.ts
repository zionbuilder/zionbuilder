import { defineStore } from 'pinia';

// TODO: add type definitions for this postLockUser
export const useUserStore = defineStore('user', {
	state: () => {
		return {
			lockedUserInfo: null,
		};
	},
	getters: {
		isPostLocked: state => state.lockedUserInfo && !!state.lockedUserInfo.message,
	},
	actions: {
		setPostLock(lockData) {
			this.lockedUserInfo = lockData;
		},
		takeOverPost() {
			this.lockedUserInfo = {};
		},
	},
});
