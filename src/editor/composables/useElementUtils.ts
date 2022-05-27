import { computed } from 'vue';
import { get } from 'lodash-es';
import { useContentStore } from '../store';
import { useElementTypes } from './useElementTypes';
import { applyFilters } from '@/common/modules/hooks';

export function useElementUtils(elementUID: string) {
	const contentStore = useContentStore();

	// TODO: make this a store
	const elementTypes = useElementTypes();

	// Computed
	const element = computed(() => contentStore.getElement(elementUID));
	const elementDefinition = computed(() =>
		elementTypes.getElementType(element.value ? element.value.element_type : ''),
	);
	const elementName = computed({
		get: () => get(element, '_advanced_options._element_name', elementDefinition.value.name),
		set: newValue => contentStore.updateElement(elementUID, '_advanced_options._element_name', newValue),
	});
	const isVisible = computed(() => getOption('_isVisible', true));
	const isWrapper = elementDefinition.value.wrapper;

	// Actions
	function getElementCssId() {
		const cssID = getOption('_advanced_options._element_id', elementUID);
		// TODO: this filter has changed removing 'this' and replacing it with elementUID
		return applyFilters('zionbuilder/element/css_id', cssID, elementUID);
	}

	function highlight() {
		contentStore.updateElement(elementUID, 'isVisible', true);
	}

	function unHighlight() {
		contentStore.updateElement(elementUID, 'isVisible', false);
	}

	function getOption(path: string, defaultValue: unknown = null) {
		return get(element.value?.options, path, defaultValue);
	}

	function updateOption(path: string, newValue: unknown) {
		contentStore.updateElement(elementUID, `options.${path}`, newValue);
	}

	function toggleVisibility() {
		updateOption('_isVisible', !isVisible.value);
	}

	return {
		// computed
		element,
		elementDefinition,
		elementName,
		isVisible,
		isWrapper,

		// Methods
		getOption,
		updateOption,
		highlight,
		unHighlight,
		toggleVisibility,
		getElementCssId,
	};
}
