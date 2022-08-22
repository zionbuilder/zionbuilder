import { computed } from 'vue';
import { get } from 'lodash-es';
import { useContentStore, useElementDefinitionsStore } from '../store';
import { applyFilters } from '/@/common/modules/hooks';

export function useElementUtils(element: ZionElement) {
	const contentStore = useContentStore();
	const elementsDefinitionStore = useElementDefinitionsStore();

	// Computed
	const elementDefinition = computed(() =>
		elementsDefinitionStore.getElementDefinition(element ? element.element_type : ''),
	);
	const elementName = computed({
		get: () => get(element, '_advanced_options._element_name', elementDefinition.value.name),
		set: newValue => contentStore.updateElement(element.uid, '_advanced_options._element_name', newValue),
	});
	const isVisible = computed(() => getOption('_isVisible', true));
	const isWrapper = elementDefinition.value.wrapper;

	function highlight() {
		contentStore.updateElement(element.uid, 'isVisible', true);
	}

	function unHighlight() {
		contentStore.updateElement(element.uid, 'isVisible', false);
	}

	function getOption(path: string, defaultValue: unknown = null) {
		return get(element.options, path, defaultValue);
	}

	function updateOption(path: string, newValue: unknown) {
		contentStore.updateElement(element.uid, `options.${path}`, newValue);
	}

	function toggleVisibility() {
		updateOption('_isVisible', !isVisible.value);
	}

	return {
		// computed
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
	};
}
