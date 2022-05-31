import { defineStore } from 'pinia';
import { pull, set } from 'lodash-es';
import { get } from 'lodash-es';
import { useElementDefinitionsStore } from './useElementDefinitionsStore';

interface State {
	areas: BuilderArea[];
	elements: ZionElement[];
}

export const useContentStore = defineStore('content', {
	state: (): State => {
		return {
			areas: [],
			elements: [],
		};
	},
	getters: {
		getArea: state => (areaID: string) => state.areas.find(area => area.id === areaID),
		getElement: state => (elementUID: string) =>
			state.elements.find(element => element.uid === elementUID) || {
				uid: elementUID,
				element_type: 'invalid',
				options: {},
				content: [],
			},
		getElementName() {
			return (element: ZionElement): string => {
				const elementName = <string>get(element.options, '_advanced_options._element_name');

				if (elementName) {
					return elementName;
				} else {
					const elementsDefinitionStore = useElementDefinitionsStore();
					const elementDefinition = elementsDefinitionStore.getElementDefinition(element.element_type);
					return elementDefinition.name;
				}
			};
		},
	},
	actions: {
		registerArea(areaConfig: BuilderArea, areaContent: ZionElementConfig[]) {
			const rootElement = {
				uid: areaConfig.id,
				element_type: 'contentRoot',
				content: areaContent,
				options: {},
			};

			// Extract elements
			this.registerElement(rootElement);

			// Register the area
			this.areas.push(areaConfig);
		},
		registerElement(elementConfig: ZionElementConfig, parent: ZionElement['parent'] = null) {
			const newElement: ZionElement = {
				...elementConfig,
				content: elementConfig.content.map(el => el.uid),
				parent: parent,
			};

			this.elements.push(newElement);

			elementConfig.content.forEach(el => this.registerElement(el, elementConfig.uid));
		},

		clearAreaContent(areaID: string) {
			const areaElement = this.getElement(areaID);

			if (areaElement) {
				// Delete child elements
				areaElement.content.forEach(elementUID => {
					this.deleteElement(elementUID);
				});

				areaElement.content = [];
			}
		},
		deleteElement(elementUID: string) {
			const element = this.getElement(elementUID);

			if (element) {
				pull(this.elements, element);
			}
		},
		updateElement(elementUID: string, path: string, newValue: unknown) {
			const element = this.getElement(elementUID);
			if (element) {
				set(element, path, newValue);
			}
		},
	},
});
