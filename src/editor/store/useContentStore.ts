import { defineStore } from 'pinia';
import { pull, set } from 'lodash-es';
import { get } from 'lodash-es';
import { useElementDefinitionsStore } from './useElementDefinitionsStore';
import { generateUID } from '/@/common/utils';
import { useHistory } from '/@/editor/composables';

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
		getElement:
			state =>
			(elementUID: string): ZionElement =>
				state.elements.find(element => element.uid === elementUID) || {
					uid: elementUID,
					element_type: 'invalid',
					options: {},
					content: [],
					parent: null,
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
		getElementIndexInParent() {
			return (element: ZionElement) => this.getElement(element.parent).content.indexOf(element.uid);
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
			const element = Object.assign(
				{},
				{
					uid: generateUID(),
					content: [],
					options: {},
				},
				elementConfig,
			);

			const content: string[] = [];

			// Register children
			element.content.forEach(el => {
				const childElement = this.registerElement(el, element.uid);
				content.push(childElement.uid);
			});

			const newElement: ZionElement = {
				...element,
				content,
				parent: parent,
				addedTime: Date.now(),
			};
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

				pull(this.elements, element);
			}
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
	},
});
