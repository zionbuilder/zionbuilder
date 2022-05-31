import { useIsDragging } from '../../composables';
import { ref, computed } from 'vue';

export function useTreeViewList(element: ZionElement) {
	// Add elements button DOM element will be populated after mount
	const addElementsPopupButton = ref(null);
	const elementOptionsRef = ref(null);
	const { setDraggingState } = useIsDragging();

	const templateItems = computed({
		get() {
			return element.content;
		},
		set(value: string[]) {
			element.content = value;
		},
	});

	function sortableStart() {
		setDraggingState(true);
	}

	function sortableEnd() {
		setDraggingState(false);
	}

	return {
		addElementsPopupButton,
		templateItems,
		elementOptionsRef,
		sortableStart,
		sortableEnd,
	};
}
