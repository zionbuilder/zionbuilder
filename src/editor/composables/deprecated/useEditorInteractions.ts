import { useUI } from '../useUI'

export function useEditorInteractions() {
	console.warn(`useEditorInteractions was deprecated in v3.0.0. You can now use the 'useUI' composable API`)
	const useUIInstance = useUI()

	return useUIInstance
}