import { defineStore } from 'pinia';
import { pull, set, get } from 'lodash-es';
import { useElementDefinitionsStore } from './useElementDefinitionsStore';
import { ZionElement } from '../models/ZionElement';
import { useUIStore } from './useUIStore';

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
		contentRootElement: state => state.elements.find(element => element.uid === window.ZnPbInitialData.page_id),
		getArea: state => (areaID: string) => state.areas.find(area => area.id === areaID),
		getAreaContentAsJSON(state) {
			return (areaID: string) => {
				const area = state.areas.find(area => area.id === areaID);

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
				this.areas.splice(existingAreaIndex, 1, areaConfig);
			} else {
				this.areas.push(areaConfig);
			}
		},
		registerElement(elementConfig: ZionElementConfig, parentUID = '') {
			const newElement: ZionElement = new ZionElement(elementConfig, parentUID);

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

			if (element) {
				// Delete from parent
				if (element.parent) {
					pull(element.parent.content, element.uid);
				}

				// Delete all children
				if (element.content) {
					[...element.content].forEach(childUID => this.deleteElement(childUID));
				}

				// Close the edit panel
				const UIStore = useUIStore();

				// Close the options panel if this element is active
				if (UIStore.editedElementUID === element.uid) {
					UIStore.unEditElement();
				}

				pull(this.elements, element);
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

			const elementClone = element.getClone();
			const newElement = this.addElement(elementClone, element.parentUID, element.indexInParent + 1);

			return newElement;
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
			const addedElementsUIDs = [];
			elements.forEach(element => {
				addedElementsUIDs.push(this.addElement(element, parent, index));

				// Update the index
				index = index !== -1 ? index + 1 : index;
			});

			return addedElementsUIDs;
		},
	},
});
