import { __ } from '@wordpress/i18n';
import { markRaw } from 'vue';
import { defineStore } from 'pinia';
import { find } from 'lodash-es';
import { ElementType } from '../models/ElementType';

type ElementCategory = {
	id: string;
	name: string;
	priority: number;
};

export const useElementDefinitionsStore = defineStore('elementDefinitions', {
	state: (): {
		elementsDefinition: ElementType[];
		categories: ElementCategory[];
	} => {
		return {
			elementsDefinition: [
				new ElementType({
					element_type: 'contentRoot',
					wrapper: true,
					show_in_ui: false,
					name: __( 'Root', 'zionbuilder' ),
				}),
			],
			categories: [],
		};
	},
	getters: {
		getVisibleElements: state => state.elementsDefinition.filter(element => element.show_in_ui),
		getElementDefinition: state => (elementType: string) =>
			find(state.elementsDefinition, { element_type: elementType }) ||
			new ElementType({
				element_type: 'invalid',
				name: __( 'Invalid Element', 'zionbuilder' ),
			}),
		getElementIcon() {
			return (elementType: string) => {
				const element = this.getElementDefinition(elementType);
				return element.icon ? element.icon : null;
			};
		},
		getElementImage() {
			return (elementType: string) => {
				const element = this.getElementDefinition(elementType);
				return element.thumb ? element.thumb : null;
			};
		},
		getElementTypeCategory:
			state =>
			(id: string): ElementCategory | undefined =>
				find(state.categories, { id }),
	},
	actions: {
		setCategories(categories: ElementCategory[]) {
			this.categories = categories;
		},
		addElements(elements: []) {
			elements.forEach(elementConfig => {
				this.addElement(elementConfig);
			});
		},
		addElement(config) {
			this.elementsDefinition.push(new ElementType(config));
		},
		registerElementComponent({ elementType, component }) {
			const element = this.getElementDefinition(elementType);

			if (!element) {
				console.warn(`element with ${elementType} could not be found.`);
				return;
			}

			element.component = markRaw(component);
		},
	},
});
