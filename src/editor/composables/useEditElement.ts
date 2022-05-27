import { ref } from 'vue';
import { useUIStore } from '../store';

const element = ref(null);

export function useEditElement() {
	const editElement = elementInstance => {
		const { openPanel } = useUIStore();

		element.value = elementInstance;

		openPanel('panel-element-options');
	};

	const unEditElement = () => {
		const { closePanel } = useUIStore();

		element.value = null;
		closePanel('panel-element-options');
	};

	return {
		element,
		editElement,
		unEditElement,
	};
}
