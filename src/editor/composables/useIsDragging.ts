import { ref, Ref } from 'vue';

const isDragging: Ref = ref(false);

export function useIsDragging() {
	const setDraggingState = (newValue: boolean) => {
		isDragging.value = newValue;
	};
	return {
		setDraggingState,
		isDragging,
	};
}
