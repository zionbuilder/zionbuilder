import { useElementActions, useElementMenu } from '@composables'
import { ref, computed } from 'vue'

export function useTreeViewItem(props: Object) {
	const { focusedElement } = useElementActions()
	const elementOptionsRef = ref(null)
	const isActiveItem = computed(() => focusedElement.value === props.element)

	const showElementMenu = function () {
		const { showElementMenu } = useElementMenu()
		showElementMenu(props.element, elementOptionsRef.value)
	}

	return {
		elementOptionsRef,
		isActiveItem,
		showElementMenu
	}
}