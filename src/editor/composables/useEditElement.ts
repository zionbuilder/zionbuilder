import { ref } from 'vue';
import { useUIStore } from '../store';

const element = ref(null);

export function useEditElement() {
	const editElement = elementInstance => {
		const { openPanel } = useUIStore();

		element.value = elementInstance;

		openPanel('panel-element-options');

		if (elementInstance) {
			let currentElement = elementInstance.parent;
			while (currentElement.parent) {
				currentElement.treeViewItemExpanded = true;
				currentElement = currentElement.parent;
			}
		}
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
