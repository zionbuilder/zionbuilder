import { ref, Ref } from 'vue';
import { Element } from '../models';
import { useUIStore } from '../store';

const activeElementMenu: Ref<null | object> = ref(null);

export function useElementMenu() {
	const showElementMenu = (element: Element, selector, actions = {}) => {
		// Don't show on preview mode
		const UIStore = useUIStore();

		if (UIStore.isPreviewMode.value) {
			return;
		}

		activeElementMenu.value = {
			element,
			selector,
			key: Math.random(),
			actions,
		};
	};

	const showElementMenuFromEvent = (element, event, actions = {}) => {
		showElementMenu(
			element,
			{
				ownerDocument: event.view.document,
				getBoundingClientRect() {
					return {
						width: 0,
						height: 0,
						top: event.clientY,
						left: event.clientX,
					};
				},
			},
			actions,
		);
	};

	const hideElementMenu = () => {
		activeElementMenu.value = null;
	};

	return {
		showElementMenu,
		showElementMenuFromEvent,
		hideElementMenu,
		activeElementMenu,
	};
}
