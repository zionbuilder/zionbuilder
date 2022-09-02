import { ref, Ref } from 'vue';
import { cloneDeep, merge, get, set } from 'lodash-es';
import { useLocalStorage } from './useLocalStorage';

const copiedElement: Ref<{
	element?: ZionElement;
	action?: string;
}> = ref({});

interface ElementCopiedStyles {
	styles: Record<string, unknown>;
	custom_css: string;
}

const copiedElementStyles: Ref<null | ElementCopiedStyles> = ref(null);

export function useElementActions() {
	const { addData, getData, removeData } = useLocalStorage();

	const copyElement = (element: ZionElement, action = 'copy') => {
		copiedElement.value = {
			element,
			action,
		};

		if (action === 'cut') {
			element.isCut = true;

			removeData('copiedElement');
		} else if (action === 'copy') {
			// Copy styles
			copyElementStyles(element);

			// Copy css classes
			copyElementClasses(element);

			// Save to localStorage for cross site
			addData('copiedElement', element.toJSON());
		}
	};

	const pasteElement = (element: ZionElement) => {
		let insertElement = element;
		let index = -1;

		const elementForPaste = copiedElement.value.element
			? copiedElement.value.element.getClone()
			: getData('copiedElement');

		if (!elementForPaste) {
			return;
		}

		// If the element is not a wrapper, add to parent element
		if (element.parent && (!element.isWrapper || elementForPaste.uid === element.uid)) {
			insertElement = element.parent;
			index = element.indexInParent + 1;
		}

		if (copiedElement.value.action === 'cut' && copiedElement.value.element) {
			if (copiedElement.value.element === element) {
				copiedElement.value.element.isCut = false;
				copiedElement.value = {};
			} else {
				copiedElement.value.element.isCut = false;

				window.zb.run('editor/elements/move', {
					newParent: insertElement,
					element: copiedElement.value.element,
					index,
				});
			}

			copiedElement.value = {};
		} else {
			window.zb.run('editor/elements/copy', {
				parent: insertElement,
				copiedElement: elementForPaste,
				index,
			});
		}
	};

	const resetCopiedElement = () => {
		if (copiedElement.value && copiedElement.value.element && copiedElement.value.action === 'cut') {
			copiedElement.value.element.isCut = false;
		}
		copiedElement.value = {};
	};

	const copyElementStyles = (element: ZionElement) => {
		const dataForSave = {
			styles: <Record<string, unknown>>cloneDeep(element.options._styles),
			custom_css: get(element, 'options._advanced_options._custom_css', ''),
		};

		copiedElementStyles.value = dataForSave;

		// Save to localStorage for cross site
		addData('copiedElementStyles', dataForSave);
	};

	const pasteElementStyles = (element: ZionElement) => {
		const styles = getData('copiedElementStyles');

		if (!styles) {
			return;
		}

		window.zb.run('editor/elements/paste-styles', {
			element,
			styles,
		});
	};

	const copyElementClasses = (element: ZionElement) => {
		const dataToSave = cloneDeep(get(element.options, '_styles.wrapper.classes', null));

		// Save to localStorage for cross site
		addData('copiedElementClasses', dataToSave);
	};

	const pasteElementClasses = (element: ZionElement) => {
		const classes = getData('copiedElementClasses');

		if (classes) {
			window.zb.run('editor/elements/paste-css-classes', {
				element,
				classes,
			});
		}
	};

	return {
		copyElement,
		pasteElement,
		resetCopiedElement,
		copiedElement,
		copyElementStyles,
		pasteElementStyles,
		copiedElementStyles,
		// Copy element classes
		copyElementClasses,
		pasteElementClasses,
	};
}
