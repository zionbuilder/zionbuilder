import { ref, Ref } from 'vue'

const getIsPro: Ref<Boolean> = ref(false)

export function useIsPro() {
	const isPro = () => {

		getIsPro.value = window.ZnPbAdminPageData.is_pro_active
		return getIsPro.value
	}
	return {
		isPro
	}
}