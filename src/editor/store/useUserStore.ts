import { defineStore } from 'pinia';

type Permissions = {
	lockedUserInfo: Record<string, unknown> | null;
	permissions: {
		allow_access: boolean;
		only_content: boolean;
	};
};

// TODO: add type definitions for this postLockUser
export const useUserStore = defineStore('user', {
	state: (): Permissions => {
		return {
			lockedUserInfo: null,
			permissions: window.ZnPbInitialData.user_permissions,
		};
	},
	getters: {
		isPostLocked: state => state.lockedUserInfo && !!state.lockedUserInfo.message,
		userCanEditContent: state => !state.permissions.only_content,
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
