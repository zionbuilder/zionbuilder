import { defineStore } from 'pinia';
import { pull, set, get } from 'lodash-es';
import { useElementDefinitionsStore } from './useElementDefinitionsStore';
import { generateUID } from '/@/common/utils';
import { useHistory } from '/@/editor/composables';
import { ZionElement } from '../models/ZionElement';

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
		getAreaContentAsJSON(state) {
			return (areaID: string) => {
				const area = state.areas.find(area => area.id === areaID);
				console.log({ area });
				if (area) {
					return area.element.content.map(childUID => {
						const element = this.getElement(childUID);
						return element.toJSON();
					});
				}

				return [];
			};
		},
		getElement:
			state =>
			(elementUID: string): ZionElement | null =>
				state.elements.find(element => element.uid === elementUID) || null,
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
		getElementIndexInParent() {
			return (element: ZionElement) => element.parent?.content.indexOf(element.uid);
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

			// Register the area
			areaConfig.element = this.registerElement(rootElement);

			// Check if the area was already registered
			const existingAreaIndex = this.areas.findIndex(area => area.id === areaConfig.id);
			if (existingAreaIndex >= 0) {
				console.log('existing', existingAreaIndex);

				this.areas.splice(existingAreaIndex, 1, areaConfig);
			} else {
				this.areas.push(areaConfig);
			}
		},
		registerElement(elementConfig: ZionElementConfig, parent = '') {
			const newElement: ZionElement = new ZionElement(elementConfig, parent);

			// Check if the area was already registered
			const existingElementIndex = this.elements.findIndex(element => element.uid === newElement.uid);
			if (existingElementIndex >= 0) {
				this.elements.splice(existingElementIndex, 1, newElement);
			} else {
				this.elements.push(newElement);
			}

			return newElement;
		},
		clearAreaContent(areaID: string) {
			const areaElement = this.getElement(areaID);

			if (areaElement) {
				// Delete child elements
				[...areaElement.content].forEach(elementUID => {
					this.deleteElement(elementUID);
				});

				areaElement.content = [];
			}
		},
		deleteElement(elementUID: string) {
			const element = this.getElement(elementUID);
			console.log(elementUID);
			if (element) {
				// Delete from parent
				if (element.parent) {
					pull(element.parent.content, element.uid);
				}

				// Delete all children
				if (element.content) {
					[...element.content].forEach(childUID => this.deleteElement(childUID));
				}

				pull(this.elements, element);
				console.log(this.elements);
			} else {
				console.log('element with uid not found');
			}
		},
		getElementValue(elementUID: string, path: string, defaultValue = null) {
			const element = this.getElement(elementUID);
			if (element) {
				return get(element, path, defaultValue);
			}

			return defaultValue;
		},
		updateElement(elementUID: string, path: string, newValue: unknown) {
			const element = this.getElement(elementUID);
			if (element) {
				set(element, path, newValue);
			}
		},
		duplicateElement(element: ZionElement) {
			if (!element.parent) {
				return;
			}

			const indexInParent = element.parent.content.indexOf(element.uid);
			const elementClone = <ZionElement>JSON.parse(JSON.stringify(element));

			// Replace the element unique id
			elementClone.uid = generateUID();

			if (elementClone.content.length > 0) {
				elementClone.content = elementClone.content.map(childUID => {
					const childElement = this.getElement(childUID);
					return this.duplicateElement(childElement).uid;
				});
			}

			element.parent.content.splice(indexInParent + 1, 0, elementClone.uid);
			this.elements.push(elementClone);

			const { addToHistory } = useHistory();
			addToHistory(`Duplicated ${this.getElementName(element.uid)}`);

			return elementClone;
		},
		addElement(elementConfig: ZionElementConfig, parentUID: string, index: number) {
			const parent = this.getElement(parentUID);

			if (!parent) {
				return null;
			}

			const newElement = this.registerElement(elementConfig, parentUID);

			parent.content.splice(index, 0, newElement.uid);

			return newElement;
		},
		addElements(elements: ZionElementConfig[], parent: ZionElement, index = -1) {
			elements.forEach(element => {
				this.addElement(element, parent, index);

				// Update the index
				index = index !== -1 ? index + 1 : index;
			});
		},
	},
});
