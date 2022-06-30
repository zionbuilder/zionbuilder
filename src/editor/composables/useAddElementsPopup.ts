import { ref, Ref } from 'vue';

const activePopup: Ref<null | object> = ref(null);
const shouldOpenPopup = ref(false);

export function useAddElementsPopup() {
	const getElementForInsert = () => {
		const { element, config } = activePopup.value;
		const { placement = 'inside' } = config;
		if (placement === 'inside' && element.isWrapper) {
			return {
				element,
			};
		} else {
			const index = element.getIndexInParent() + 1;

			return {
				element: element.parent,
				index,
			};
		}
	};

	const insertElement = newElement => {
		const { element, index = -1 } = getElementForInsert();
		newElement = Array.isArray(newElement) ? newElement : [newElement];
		element.addChildren(newElement, index);
	};

	return {
		getElementForInsert,
		insertElement,
		activePopup,
		shouldOpenPopup,
	};
}
