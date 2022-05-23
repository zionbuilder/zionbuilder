import { Ref, ref } from 'vue';
import { defineStore } from 'pinia';
import { Element } from '../composables/models/Element';

export const useElementsStore = defineStore('elements', () => {
	// Global template parts store
	const elements: Ref<{ [key: string]: Element }> = ref({});

	const registerElement = (config, parent: string): Element => {
		const element = new Element(config, parent);
		elements.value[element.uid] = element;

		return element;
	};

	const unregisterElement = (uid: string) => {
		delete elements.value[uid];
	};

	const getElement = (elementUID: string) => {
		return elements.value[elementUID];
	};

	return {
		elements,
		registerElement,
		unregisterElement,
		getElement,
	};
});
