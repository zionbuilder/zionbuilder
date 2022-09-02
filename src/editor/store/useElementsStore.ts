import { defineStore } from 'pinia';
import { Element } from '../models/Element';

export const useElementsStore = defineStore('elements', {
	state(): {
		elements: Record<string, Element>;
	} {
		return {
			elements: {},
		};
	},
	actions: {
		registerElement(config, parent: string) {
			const element = new Element(config, parent);
			this.elements[element.uid] = element;

			return element;
		},
		unregisterElement(uid: string) {
			delete this.elements[uid];
		},
		getElement(elementUID: string) {
			return this.elements[elementUID];
		},
	},
});
