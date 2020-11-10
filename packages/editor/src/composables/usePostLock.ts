import { fromPairs } from "lodash-es"
import { computed, ref } from 'vue'

const lockedUserInfo = ref(window.ZnPbInitalData.post_lock_user)

export const usePostLock = () => {
	const isPostLocked = computed(() => lockedUserInfo.value && !!lockedUserInfo.value.message)

	const takeOverPost = () => {
		lockedUserInfo.value = {}
	}

	return {
		isPostLocked,
		lockedUserInfo,
		takeOverPost
	}
}