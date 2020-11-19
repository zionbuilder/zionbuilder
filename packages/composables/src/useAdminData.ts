import { ref, Ref } from 'vue'
const adminData = ref(window.ZnPbAdminPageData)

export const useAdminData = () => {
	return {
		adminData
	}
}