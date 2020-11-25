import { useElements, useAddElementsPopup, useElementMenu, useIsDragging } from '@composables'
import { ref, computed } from 'vue'

export function useTreeViewList(props: Object) {
	// Add elements button DOM element will be populated after mount
	const addElementsPopupButton = ref(null)
	const elementOptionsRef = ref(null)
	const { getElement } = useElements()
	const { setDraggingState } = useIsDragging()

	const addButtonBgColor = props.element.element_type === 'zion_column' ? '#eec643' : '#404be3'

	const templateItems = computed({
		get() {
			return props.element.content
		},
		set(value) {
			props.element.content = value
		}
	})

	function toggleAddElementsPopup() {
		const { showAddElementsPopup } = useAddElementsPopup()

		showAddElementsPopup(props.element, addElementsPopupButton)
	}

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
		addButtonBgColor,
		elementOptionsRef,
		toggleAddElementsPopup,
		sortableStart,
		sortableEnd,
		showElementMenu
	}
}