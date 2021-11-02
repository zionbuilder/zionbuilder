import { useElementMenu, useEditElement, useElementTypes } from '@composables'
import { ref, computed } from 'vue'

export function useTreeViewItem(props: Object) {
	const { element: activeEditElement } = useEditElement();
	const elementOptionsRef = ref(null)
	const isActiveItem = computed(() => activeEditElement.value === props.element)

	const { getElementType } = useElementTypes();

	const elementModel = getElementType(props.element.element_type);

	const showElementMenu = function () {
		const { showElementMenu } = useElementMenu()
		showElementMenu(props.element, elementOptionsRef.value)
	}

	function editElement() {
		const { editElement } = useEditElement();

		editElement(props.element);
		props.element.scrollTo = true;
	}

	return {
		elementOptionsRef,
		isActiveItem,
		elementModel,
		showElementMenu,
		editElement
	}
}