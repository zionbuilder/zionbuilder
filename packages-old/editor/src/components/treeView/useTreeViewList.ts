import { useElementMenu, useIsDragging } from '@composables'
import { ref, computed } from 'vue'

export function useTreeViewList(props: Object) {
	// Add elements button DOM element will be populated after mount
	const addElementsPopupButton = ref(null)
	const elementOptionsRef = ref(null)
	const { setDraggingState } = useIsDragging()

	const templateItems = computed({
		get() {
			return props.element.content
		},
		set(value) {
			props.element.content = value
		}
	})

	function sortableStart() {
		setDraggingState(true)
	}

	function sortableEnd() {
		setDraggingState(false)
	}

	const showElementMenu = function () {
		const { showElementMenu } = useElementMenu()
		showElementMenu(props.element, elementOptionsRef.value)
	}

	return {
		addElementsPopupButton,
		templateItems,
		elementOptionsRef,
		sortableStart,
		sortableEnd,
		showElementMenu
	}
}