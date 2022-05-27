import { useElementMenu, useElementTypes } from '../../composables';
import { ref, computed } from 'vue';
import { useUIStore } from '@/editor/store';

export function useTreeViewItem(element) {
	const UIStore = useUIStore();
	const elementOptionsRef = ref(null);
	const isActiveItem = computed(() => UIStore.editedElementID === element.uid);

	const { getElementType } = useElementTypes();

	const elementModel = getElementType(element.element_type);

	const showElementMenu = function () {
		const { showElementMenu, activeElementMenu, hideElementMenu } = useElementMenu();
		if (activeElementMenu.value && activeElementMenu.value.element === props.element) {
			hideElementMenu();
		} else {
			showElementMenu(element, elementOptionsRef.value);
		}
	};

	return {
		elementOptionsRef,
		isActiveItem,
		elementModel,
		showElementMenu,
	};
}
