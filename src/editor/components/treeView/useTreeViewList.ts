import { ref, computed } from 'vue';
import { useUIStore } from '../../store';

export function useTreeViewList(element: ZionElement) {
	// Add elements button DOM element will be populated after mount
	const addElementsPopupButton = ref(null);
	const elementOptionsRef = ref(null);
	const UIStore = useUIStore();

	const templateItems = computed({
		get() {
			return element.content;
		},
		set(value: string[]) {
			element.content = value;
		},
	});

	function sortableStart() {
		UIStore.setElementDragging(true);
	}

	function sortableEnd() {
		UIStore.setElementDragging(false);
	}

	return {
		addElementsPopupButton,
		templateItems,
		elementOptionsRef,
		sortableStart,
		sortableEnd,
	};
}
