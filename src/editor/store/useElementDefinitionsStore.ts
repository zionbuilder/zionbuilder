import { defineStore } from 'pinia';
import { find } from 'lodash-es';
import { translate } from '@/common/modules/i18n';
import { ElementType } from '../models/ElementType';

export const useElementDefinitionsStore = defineStore('elementDefinitions', {
	state: () => {
		return {
			elementsDefinition: [
				new ElementType({
					element_type: 'contentRoot',
					wrapper: true,
					show_in_ui: false,
				}),
			],
		};
	},
	getters: {
		getVisibleElements: state => state.elementsDefinition.filter(element => element.show_in_ui),
		getElementDefinition: state => (elementType: string) =>
			find(state.elementsDefinition, { element_type: elementType }) ||
			new ElementType({
				element_type: 'invalid',
				name: translate('invalid_element'),
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
	},
	actions: {
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

			element.component = component;
		},
	},
});
