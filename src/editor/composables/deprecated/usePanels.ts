import { useUI } from '../useUI'

export function usePanels() {
	console.warn(`usePanels was deprecated in v3.0.0. You can now use the 'useUI' composable API`)
	const useUIInstance = useUI()

	return useUIInstance
}