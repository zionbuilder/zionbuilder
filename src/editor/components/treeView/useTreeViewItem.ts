import { ref, computed } from 'vue';
import { useUIStore, useElementDefinitionsStore } from '/@/editor/store';

export function useTreeViewItem(element: ZionElement) {
	const UIStore = useUIStore();
	const elementOptionsRef = ref(null);
	const isActiveItem = computed(() => UIStore.editedElement === element);

	const elementsDefinitionStore = useElementDefinitionsStore();
	const elementModel = elementsDefinitionStore.getElementDefinition(element);

	const showElementMenu = function () {
		if (UIStore.activeElementMenu && UIStore.activeElementMenu.elementUID === element.uid) {
			UIStore.hideElementMenu();
		} else {
			UIStore.showElementMenu(element, elementOptionsRef.value);
		}
	};

	return {
		elementOptionsRef,
		isActiveItem,
		elementModel,
		showElementMenu,
	};
}
