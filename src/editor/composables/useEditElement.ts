import { ref } from 'vue';
import { useUI } from './useUI';

const element = ref(null);

export function useEditElement() {
	const editElement = elementInstance => {
		const { openPanel } = useUI();

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
		const { closePanel } = useUI();

		element.value = null;
		closePanel('panel-element-options');
	};

	return {
		element,
		editElement,
		unEditElement,
	};
}
