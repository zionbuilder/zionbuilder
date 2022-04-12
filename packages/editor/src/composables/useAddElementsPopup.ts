import { ref, Ref } from 'vue';

const activePopup: Ref<null | object> = ref(null);
const shouldOpenPopup = ref(false);

export function useAddElementsPopup() {
	const showAddElementsPopup = (element, selector, config = {}) => {
		if (activePopup.value && activePopup.value.element === element) {
			hideAddElementsPopup();
			return;
		}

		activePopup.value = {
			element,
			selector,
			config,
			key: Math.random(),
		};
	};

	const getElementForInsert = () => {
		const { element, config } = activePopup.value;
		const { placement = 'inside' } = config;

		if (placement === 'inside' && (element.isWrapper || element.element_type === 'contentRoot')) {
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

	const hideAddElementsPopup = () => {
		activePopup.value = null;
	};

	return {
		showAddElementsPopup,
		hideAddElementsPopup,
		getElementForInsert,
		insertElement,
		activePopup,
		shouldOpenPopup,
	};
}
