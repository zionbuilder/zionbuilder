import { useElements, useAddElementsPopup } from '@data'
import { ref, computed } from 'vue'

export function useTreeView(props: Object) {
		// Add elements button DOM element will be populated after mount
		const addElementsPopupButton = ref(null)
		const { getElement } = useElements()

		const addButtonBgColor = props.element.element_type === 'zion_column' ? '#eec643' : '#404be3'

		const templateItems = computed({
			get () {
				return props.element.content.map(elementUID => {
					return getElement(elementUID)
				})
			},
			set (value) {
				console.log({value})
			}
		})

		function toggleAddElementsPopup () {
			const { showAddElementsPopup } = useAddElementsPopup()

			showAddElementsPopup(props.element, addElementsPopupButton)
		}

		function sortableStart () {
			// setDraggingState(true)
		}

		function sortableEnd () {
			// setDraggingState(false)
		}

		return {
			addElementsPopupButton,
			templateItems,
			addButtonBgColor,
			toggleAddElementsPopup,
			sortableStart,
			sortableEnd
		}
}