import { ref, Ref } from 'vue';
import { cloneDeep, merge, get, set } from 'lodash-es';
import { Element } from './models/Element';
import { useHistory } from './useHistory';
import { useElements } from './useElements';
import { useLocalStorage } from './useLocalStorage';
import { translate } from '@zb/i18n';
import { regenerateUIDs } from '@utils';

const copiedElement: Ref<object> = ref({
	element: null,
	action: null,
});

interface ElementCopiedStyles {
	styles: string;
	custom_css: string;
}

const copiedElementStyles: Ref<null | ElementCopiedStyles> = ref(null);

// Preserve focused element on history change
const { addToHistory } = useHistory();

export function useElementActions() {
	const { addData, getData, removeData } = useLocalStorage();

	const copyElement = (element: Element, action = 'copy') => {
		copiedElement.value = {
			element,
			action,
		};

		if (action === 'cut') {
			element.isCutted = true;

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

	const pasteElement = element => {
		let insertElement = element;
		let index = -1;

		const elementForPaste = copiedElement.value.element
			? copiedElement.value.element.getClone()
			: getData('copiedElement');

		if (!elementForPaste) {
			return;
		}

		// If the element is not a wrapper, add to parent element
		if (!element.isWrapper || elementForPaste.uid === element.uid) {
			insertElement = element.parent;
			index = element.getIndexInParent() + 1;
		}

		if (copiedElement.value.action === 'cut' && copiedElement.value.element) {
			if (copiedElement.value.element === element) {
				copiedElement.value.element.isCutted = false;
				copiedElement.value = {};
			} else {
				copiedElement.value.element.isCutted = false;
				copiedElement.value.element.move(insertElement, index);
				addToHistory(`${translate('moved')} ${copiedElement.value.element.name}`);
			}

			copiedElement.value = {};
		} else {
			insertElement.addChild(regenerateUIDs(elementForPaste), index);
			addToHistory(`${translate('copied')} ${elementForPaste.name}`);
		}
	};

	const resetCopiedElement = () => {
		if (copiedElement.value && copiedElement.value.action === 'cut') {
			copiedElement.value.element.isCutted = false;
		}
		copiedElement.value = {};
	};

	const copyElementStyles = (element: Element) => {
		const dataForSave = {
			styles: cloneDeep(element.options._styles),
			custom_css: get(element, 'options._advanced_options._custom_css', ''),
		};

		copiedElementStyles.value = dataForSave;

		// Save to localStorage for cross site
		addData('copiedElementStyles', dataForSave);
	};

	const pasteElementStyles = element => {
		const styles = getData('copiedElementStyles');

		if (!styles) {
			return;
		}

		// Element styles
		if (styles.styles) {
			if (!element.options._styles) {
				set(element, 'options._styles', styles.styles);
			} else {
				merge(element.options._styles, styles.styles);
			}
		}

		// Copy custom css
		if (styles.custom_css.length) {
			const existingStyles = get(element, 'options._advanced_options._custom_css', '');
			const combinedStyles = existingStyles + styles.custom_css;
			set(element, 'options._advanced_options._custom_css', combinedStyles);
		}
	};

	const copyElementClasses = (element: Element) => {
		const dataToSave = cloneDeep(get(element.options, '_styles.wrapper.classes', null));

		// Save to localStorage for cross site
		addData('copiedElementClasses', dataToSave);
	};

	const pasteElementClasses = element => {
		const classes = getData('copiedElementClasses');

		merge(element.options, {
			_styles: {
				wrapper: {
					classes,
				},
			},
		});
	};

	function wrapInContainer(element) {
		const { registerElement } = useElements();
		const parent = element.parent;
		const newElement = registerElement(
			{
				element_type: 'container',
			},
			parent,
		);

		newElement.addChild(element);
		parent.replaceChild(element, newElement);

		console.log(element.parent);
	}

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
		wrapInContainer,
	};
}
