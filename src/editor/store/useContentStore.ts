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

			this.areas.push(areaConfig);
		},
		registerElement(elementConfig: ZionElementConfig, parent = '') {
			const newElement: ZionElement = new ZionElement(elementConfig, parent);
			this.elements.push(newElement);

			return newElement;
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
				// Delete from parent
				const parentElement = this.getElement(element.parent);
				parentElement.content;
				pull(parentElement.content, element.uid);

				// Delete all children
				if (element.content) {
					element.content.forEach(child => this.deleteElement(child));
				}

				pull(this.elements, element);
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

			const parent = this.getElement(element.parent);
			const indexInParent = parent.content.indexOf(element.uid);
			const elementClone = <ZionElement>JSON.parse(JSON.stringify(element));

			// Replace the element unique id
			elementClone.uid = generateUID();

			if (elementClone.content.length > 0) {
				elementClone.content = elementClone.content.map(childUID => {
					const childElement = this.getElement(childUID);
					return this.duplicateElement(childElement).uid;
				});
			}

			parent.content.splice(indexInParent + 1, 0, elementClone.uid);
			this.elements.push(elementClone);

			const { addToHistory } = useHistory();
			addToHistory(`Duplicated ${this.getElementName(element.uid)}`);

			return elementClone;
		},
		addElement(elementConfig: ZionElementConfig, parent: ZionElement, index: number) {
			const ZionElement = this.registerElement(elementConfig, parent.uid);

			parent.content.splice(index, 0, ZionElement.uid);
		},
		addElements(elements: ZionElementConfig[], parent: ZionElement, index = -1) {
			elements.forEach(element => {
				this.addElement(element, parent, index);

				// Update the index
				index = index !== -1 ? index + 1 : index;
			});
		},
		setElementVisibility(elementUID: string, newValue: boolean) {
			this.updateElement(elementUID, 'options._isVisible', newValue);
		},
	},
});
