import { ref } from 'vue'

const isDemoMode = ref(window.ZnPbDemoMode && ZnPbDemoMode.enabled)

export function useDemoMode() {
	function setDemoMode( status: boolean ): void {
		isDemoMode.value = status
	}

	return {
		isDemoMode,
		setDemoMode
	}
}